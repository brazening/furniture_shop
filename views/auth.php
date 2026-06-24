
<style>


    /* .button_auth {
        background-color: #A6B1B4;
        width: 250px;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: auto; /* Кнопка всегда прижимается вниз 
    }
    
    

    ul .navbar-nav {
        flex-direction: row;
    }

    a {
        text-underline-offset: 2px;
        text-decoration: none; /* Удаляем текущее подчёркивание */
     /* } 

    a:hover {
        text-decoration: underline; /* Добавляем текущее подчёркивание */
    /* } */


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
    .button_auth {
        /* background: #A6B1B4;
        width: 250px;
        padding: 12px 24px;
        font-size: 1.05rem;
        border-radius: 8px;
        color: black;
        margin: 1.5rem auto 0;
        display: block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); */
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

    .button_auth:hover {
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


<!-- <div class="row-12 h3 text-center">Авторизация</div>
<form method="post">
    <input type="hidden" name="action" value="auth">
    <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon1">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-add" viewBox="0 0 16 16">
                <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Zm-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                <path d="M2 13c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Z"/>
            </svg>
        </span>
        <div class="form-floating">
            <input type="text" name="login" class="form-control" placeholder="Логин" aria-label="Логин" aria-describedby="basic-addon1">
            <label>Логин</label>
        </div>
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon1">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lock" viewBox="0 0 16 16">
                <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 5.996V14H3s-1 0-1-1 1-4 6-4c.564 0 1.077.038 1.544.107a4.524 4.524 0 0 0-.803.918A10.46 10.46 0 0 0 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h5ZM9 13a1 1 0 0 1 1-1v-1a2 2 0 1 1 4 0v1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-2Zm3-3a1 1 0 0 0-1 1v1h2v-1a1 1 0 0 0-1-1Z"/>
            </svg>
        </span>
        <div class="form-floating">
            <input type="password" name="pass" class="form-control" placeholder="Пароль" aria-label="Пароль" aria-describedby="basic-addon1">
            <label>Пароль</label>
        </div>
    </div>
    <div class="input-group mb-3">
        <a href="index.php?page=reg" style="color: black;">Если у вас нет аккаунта, то вы его можете зарегистрировать!</a>
    </div>
        <button type="submit" class="button_auth">Войти</button>
</form> -->


<div class="auth-container">
    <div class="auth-title">Вход в аккаунт</div>
    <form method="post">
        <input type="hidden" name="action" value="auth">
        
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

        <div class="text-center mb-3">
            <a href="index.php?page=reg" class="auth-link">Ещё нет аккаунта? Зарегистрируйтесь</a>
        </div>

        <button type="submit" class="button_auth">Войти →</button>
    </form>
</div>