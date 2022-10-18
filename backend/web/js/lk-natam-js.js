class FormAdmin {
  constructor() {};

  createMainForm() {
    // let loginFormContent = document.createElement('div');
    // loginFormContent.classList.add('login-form-content');
    // loginFormContent.innerHTML = `
    //   <h1 class="login__title">Вход</h1>
    //   <p class="login__text">Добро пожаловать в AMG Systems</p>
    //   <form action="" method="post" class="login-form">
    //     <div class="authorization-fields">
    //       <div class="login-group">
    //         <input id="log" type="text" name="login" class="input-login" placeholder=" ">
    //         <label for="log" class="label-login">Логин</label>
    //       </div>
    //       <div class="password-group">
    //         <input id="password" type="password" name="password" class="input-password" placeholder=" ">
    //         <label for="password" class="label-password">Пароль</label>
    //       </div>
    //     </div>
    //       <div class="button-group">
    //         <button type="submit" class="login-button">Войти</button>
    //         <a href="#" class="button-registration">Зарегистрироваться</a>
    //       </div>
    //   </form>
    //     <div class="login-restore-wrap">
    //       <button href="#" class="login-restore">
    //             Восстановить пароль
    //       </button>
    //     </div>
    // `;
    // this.handlersMainForm(loginFormContent);
    // return loginFormContent;
  }
  
  createRestoreForm() {
    let restoreFormContent = document.createElement('div');
    restoreFormContent.classList.add('restore-form-content');
    restoreFormContent.innerHTML = `
              <div class="restore-header">
              <div class="restore-text-wrap">
                <h1 class="restore__title">Восстановление пароля</h1>
                <p class="restore__text">Добро пожаловать в AMG Systems</p>
              </div>
              <img class="restore__logo" src="../img/logoAMG.svg" alt="">
            </div>
            <form action="" method="post" class="restore-form">
              <div class="restore-group">
                <input id="restore-email" type="text" name="restore-email" class="input-restore-email" placeholder=" ">
                <label for="restore-email" class="label-restore-email">E-mail</label>
              </div>
              <div class="button-group">
                <button type="submit" class="login-button">Восстановить</button>
              </div>
            </form>
            <button class="restore-prev">
              Назад
            </button>
    `;
    this.handlersRestoreForm(restoreFormContent);
    return restoreFormContent;
  }

  handlersMainForm(form) {
    let restoreBtn = form.querySelector('.login-restore');
    restoreBtn.addEventListener('click', (event) => {
      event.preventDefault();

      form.style.transition = '0.5s';
      form.style.opacity = '0';

      form.addEventListener('transitionend', (event) => {
        let isForm = document.querySelector('.restore-form-content');
        if (event.propertyName == 'opacity' & isForm === null) {
          form.remove();
          document.querySelector('.login-form-slide').append(this.createRestoreForm());
        }
      });

    })
  }

  handlersRestoreForm(form) {
    let prevBtn = form.querySelector('.restore-prev');
    prevBtn.addEventListener('click', (event) => {
      event.preventDefault();

      form.style.transition = '0.5s';
      form.style.opacity = '0';

      form.addEventListener('transitionend', (event) => {
        let isForm = document.querySelector('.login-form-content');
        if(event.propertyName == 'opacity' & isForm === null) {
          form.remove();
          document.querySelector('.login-form-slide').append(this.createMainForm());
        }
      })
    })
  }
}

// let exs = new FormAdmin;
// document.querySelector('.login-form-slide').append(exs.createMainForm())