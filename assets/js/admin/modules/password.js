const $ = require('jquery');
const zxcvbn = require('zxcvbn');

let scoreColors = [
    'danger',
    'danger',
    'warning',
    'warning',
    'success',
];

let scoreInfos = {
    "This is a top-10 common password": "Parmis le top 10 des mots de passes communs",
    "This is a top-100 common password": "Parmis le top 100 des mots de passes communs",
    "This is a very common password": "Mot de passe vraiment trop commun",
    "This is similar to a commonly used password": "Similaire à un mot de passe commun",
    "A word by itself is easy to guess": "Ce mot est trop simple à deviner",
    "Names and surnames by themselves are easy to guess": "Les noms ou les surnoms sont simples à deviner",
    "Common names and surnames are easy to guess": "Les noms ou les surnoms sont simples à deviner",
    "Straight rows of keys are easy to guess": "Combinaison de touches trop simple",
    "Short keyboard patterns are easy to guess": "Combinaison de touches trop simple",
    "Repeats like \"aaa\" are easy to guess'": "Les répétitions comme \"aaa\" sont simples à deviner",
    "Repeats like \"abcabcabc\" are only slightly harder to guess than \"abc\"": "Les répétitions comme \"abcabcabc\" sont simples à deviner",
    "Sequences like abc or 6543 are easy to guess": "Les séquences comme \"abc\" ou \"6543\" sont simples à deviner",
    "Recent years are easy to guess": "Les années sont simples à deviner",
    "Dates are often easy to guess": "Les dates sont souvent simples à deviner",
}

let checkPassword = function(password, confirmation, indicator, submit) {
    let result = zxcvbn(password.val());
    let score = result.score;
    let cols = indicator.children('.col-sm');
    let info = indicator.children('.password-strenth-info');

    info.text('');
    cols.attr('class', 'col-sm');

    for (var u = 0; u <= 5; u++) {
        let col = cols.eq(u);
        if (u <= score) {
            col.addClass('bg-' + scoreColors[score]);
        } else {
            col.addClass('bg-light');
        }
    }

    console.log(result)

    info.text(scoreInfos[result.feedback.warning]);
    info.attr('class', 'col-12 password-strenth-info text-' + scoreColors[score]);

    if (score < 4 || confirmation.val() !== password.val()) {
        submit.attr('disabled', 'disabled');
    } else {
        submit.removeAttr('disabled');
    }
}

module.exports = function() {
    let passwordNew = $('#form-password-new');
    let passwordConfirmation = $('#form-password-confirmation');
    let passwordSubmit = $('#form-password-submit');
    let passwordStrength = $('#form-password-strength');

    if (passwordStrength.length) {
        passwordNew.keyup(function() {
            checkPassword(passwordNew, passwordConfirmation, passwordStrength, passwordSubmit);
        });

        passwordNew.change(function() {
            checkPassword(passwordNew, passwordConfirmation, passwordStrength, passwordSubmit);
        });

        passwordConfirmation.keyup(function() {
            checkPassword(passwordNew, passwordConfirmation, passwordStrength, passwordSubmit);
        });

        passwordConfirmation.change(function() {
            checkPassword(passwordNew, passwordConfirmation, passwordStrength, passwordSubmit);
        });
    }
};
