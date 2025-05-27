// Файл: MyProject/assets/js/age_verification.js
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('ageVerificationModal');
    const confirmYesButton = document.getElementById('ageConfirmYes');
    const confirmNoButton = document.getElementById('ageConfirmNo');

    // Функція для встановлення cookie
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    // Функція для отримання cookie
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i=0;i < ca.length;i++) {
            let c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    // Перевіряємо, чи є cookie про підтвердження віку
    if (!getCookie('age_verified')) {
        if (modal) { // Перевіряємо, чи існує модальне вікно на сторінці
             modal.style.display = 'block'; // Показуємо модальне вікно
        }
    }

    if (confirmYesButton) {
        confirmYesButton.addEventListener('click', function() {
            setCookie('age_verified', 'true', 1); // Встановлюємо cookie на 1 день
            if (modal) modal.style.display = 'none'; // Ховаємо модальне вікно
        });
    }

    if (confirmNoButton) {
        confirmNoButton.addEventListener('click', function() {
            // Перенаправляємо на інший сайт або показуємо повідомлення
            alert('Ви повинні бути старше 18 років, щоб відвідати цей сайт.');
            // Можна перенаправити, наприклад, на google.com
            window.location.href = 'https://www.google.com';
            // Або просто не ховати модальне вікно і не давати доступ
        });
    }
});