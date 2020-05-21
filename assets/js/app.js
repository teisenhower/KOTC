import '../css/app.css';
/* Nav Controls */
const nav = document.querySelector('nav');
const targetButton = document.querySelector('#target-button');
targetButton.addEventListener('click', () => {
  nav.classList.toggle('closed');
  targetButton.classList.toggle('orange');
  console.log('CLICKED');
});

/* Button Controls */
const signButton = document.querySelector('#signin-button, #register-button');
document.querySelectorAll('form > input').forEach(item => {
  item.addEventListener('keyup', () => {
    document
      .querySelectorAll('form > input:not([type="hidden"])')
      .forEach(item => {
        if (item.value.length > 0) {
          signButton.classList.add('active', 'blue');
          signButton.classList.remove('inactive', 'dark-grey');
          signButton.disabled = false;
        } else {
          signButton.classList.remove('active', 'blue');
          signButton.classList.add('inactive', 'dark-grey');
          signButton.disabled = true;
        }
      });
  });
});
