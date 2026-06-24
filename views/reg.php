<style>
    .auth-container {
        max-width: 500px;
        margin: 2.5rem auto;
        padding: 2.5rem 3rem;
        background: #f8f9fa;
        border-radius: 14px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
    }

    .auth-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 2rem;
        margin-bottom: 2rem;
        letter-spacing: -0.8px;
    }

    .input-group {
        margin-bottom: 1.75rem;
    }

    .input-group-text {
        background: #ecf0f1;
        border: 1px solid #dee2e6;
        padding: 0.9rem;
    }

    .form-control {
        height: 55px;
        padding: 1rem;
        font-size: 1rem;
    }

    /* Возвращаем стиль кнопки из первого варианта с улучшениями */
    .button_reg {
        background-color: #A6B1B4;
        width: 250px;
        border: none;
        padding: 12px 24px;
        margin: 1.5rem auto 0;
        font-size: 1.05rem;
        border-radius: 16px;
        cursor: pointer;
        display: block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    }

    .button_reg:hover {
        background: #95a1a4;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(166, 177, 180, 0.25);
    }

    .auth-link {
        font-size: 0.95rem;
    }

    .input-icon {
        width: 24px;
        height: 24px;
    }

    @media (max-width: 768px) {
        .auth-container {
            margin: 1.5rem;
            padding: 2rem;
        }
        .auth-title {
            font-size: 1.75rem;
        }
    }

</style>

<div class="auth-container">
    <div class="auth-title">Регистрация</div>
    <form method="post">
        <input type="hidden" name="action" value="reg">
        
        <div class="input-group mb-4">
            <span class="input-group-text">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Zm-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    <path d="M2 13c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Z"/>
                </svg>
            </span>
            <input type="text" name="login" class="form-control" placeholder="Логин" required>
        </div>

        <div class="input-group mb-4">
            <span class="input-group-text">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 5.996V14H3s-1 0-1-1 1-4 6-4c.564 0 1.077.038 1.544.107a4.524 4.524 0 0 0-.803.918A10.46 10.46 0 0 0 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h5ZM9 13a1 1 0 0 1 1-1v-1a2 2 0 1 1 4 0v1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-2Zm3-3a1 1 0 0 0-1 1v1h2v-1a1 1 0 0 0-1-1Z"/>
                </svg>
            </span>
            <input type="password" name="pass" class="form-control" placeholder="Пароль" required>
        </div>

        <div class="input-group mb-4">
            <span class="input-group-text">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                </svg>
            </span>
            <input type="text" name="fio" class="form-control" placeholder="ФИО" required>
        </div>

        <div class="text-center mb-3">
            <a href="index.php?page=auth" class="auth-link">Уже есть аккаунт? Войти</a>
        </div>

        <button type="submit" class="button_reg">Зарегистрироваться →</button>
    </form>
</div>