class SessionHandler {
    constructor(options = {}) {
        this.options = {
            sessionTimeout: 60 * 60 * 1000,
            warningTime: 5 * 60 * 1000,
            checkInterval: 60 * 1000,
            loginRoute: '/logout', // default value
            logoutRoute: '/login', // default value

            ...options
        };

        this.lastActivityTime = Date.now();
        this.isPageHidden = false;
        this.warningShown = false;
        
        // Get current language from HTML tag
        this.currentLang = document.documentElement.lang || 'en';
        
        // Translations
        this.translations = {
            en: {
                sessionWarningTitle: 'Session Expiring Soon!',
                sessionWarningText: 'Your session will expire in {minutes} minutes.<br>Would you like to extend your session?',
                confirmButton: 'Yes, Extend Session',
                cancelButton: 'No, Log me out',
                sessionExtendedTitle: 'Session Extended',
                sessionExtendedText: 'Your session has been successfully extended.',
                sessionExpiredTitle: 'Session Expired',
                sessionExpiredText: 'Your session has expired. Please log in again.',
                loginAgain: 'Login Again'
            },
            ar: {
                sessionWarningTitle: 'تنبيه انتهاء الجلسة!',
                sessionWarningText: 'ستنتهي جلستك في غضون {minutes} دقائق.<br>هل تريد تمديد جلستك؟',
                confirmButton: 'نعم، تمديد الجلسة',
                cancelButton: 'لا، تسجيل الخروج',
                sessionExtendedTitle: 'تم تمديد الجلسة',
                sessionExtendedText: 'تم تمديد جلستك بنجاح.',
                sessionExpiredTitle: 'انتهت الجلسة',
                sessionExpiredText: 'انتهت جلستك. الرجاء تسجيل الدخول مرة أخرى.',
                loginAgain: 'تسجيل الدخول مرة أخرى'
            }
        };
        
        this.init();
    }

    init() {
        ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, () => this.resetTimer());
        });

        setInterval(() => this.checkSession(), this.options.checkInterval);
        document.addEventListener('visibilitychange', () => this.handleVisibilityChange());
    }

    resetTimer() {
        this.lastActivityTime = Date.now();
        this.warningShown = false;
    }

    getText(key, params = {}) {
        const lang = this.currentLang.startsWith('ar') ? 'ar' : 'en';
        let text = this.translations[lang][key] || this.translations['en'][key];
        
        // Replace any parameters in the text
        Object.keys(params).forEach(param => {
            text = text.replace(`{${param}}`, params[param]);
        });
        
        return text;
    }

    async checkSession() {
        const currentTime = Date.now();
        const timePassed = currentTime - this.lastActivityTime;
        const timeLeft = this.options.sessionTimeout - timePassed;

        if (timeLeft <= this.options.warningTime && !this.warningShown) {
            this.showSessionWarning(Math.floor(timeLeft / 1000));
        }

        if (timePassed >= this.options.sessionTimeout) {
            this.handleSessionExpired();
        }
    }

    async showSessionWarning(secondsLeft) {
        this.warningShown = true;

        try {
            const result = await swal({
                title: this.getText('sessionWarningTitle'),
                html: this.getText('sessionWarningText', { minutes: Math.floor(secondsLeft / 60) }),
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: this.getText('confirmButton'),
                cancelButtonText: this.getText('cancelButton'),
                allowOutsideClick: false,
                timer: secondsLeft * 1000,
                timerProgressBar: true
            });

            if (result.isConfirmed) {
                await this.extendSession();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = this.options.logoutRoute;
            } else if (result.dismiss === Swal.DismissReason.timer) {
                this.handleSessionExpired();
            }
        } catch (error) {
            console.error('Error showing session warning:', error);
        }
    }

    async extendSession() {
        try {
            const response = await fetch('/extend-session', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                this.resetTimer();
                swal({
                    title: this.getText('sessionExtendedTitle'),
                    text: this.getText('sessionExtendedText'),
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error('Failed to extend session');
            }
        } catch (error) {
            console.error('Error extending session:', error);
            this.handleSessionExpired();
        }
    }

    handleSessionExpired() {
        swal({
            title: this.getText('sessionExpiredTitle'),
            text: this.getText('sessionExpiredText'),
            type: 'error',
            confirmButtonText: this.getText('loginAgain'),
            allowOutsideClick: false
        }).then(() => {
            window.location.href = this.options.loginRoute;
        });
    }

    handleVisibilityChange() {
        if (document.hidden) {
            this.isPageHidden = true;
        } else {
            if (this.isPageHidden) {
                const timePassed = Date.now() - this.lastActivityTime;
                if (timePassed >= this.options.sessionTimeout) {
                    this.handleSessionExpired();
                } else if (timePassed >= (this.options.sessionTimeout - this.options.warningTime)) {
                    this.showSessionWarning(Math.floor((this.options.sessionTimeout - timePassed) / 1000));
                }
            }
            this.isPageHidden = false;
        }
    }
}

// Make it globally available
window.SessionHandler = SessionHandler;