const body = document.getElementById("quiz-form-body");
const add_form = document.getElementById("add_quiz_form");
const loader = Object.assign(document.createElement('div'), {
    className: 'loading-overlay',
    innerHTML: `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path d="M222.7 32.1c5 16.9-4.6 34.8-21.5 39.8C121.8 95.6 64 169.1 64 256c0 106 86 192 192 192s192-86 192-192c0-86.9-57.8-160.4-137.1-184.1c-16.9-5-26.6-22.9-21.5-39.8s22.9-26.6 39.8-21.5C434.9 42.1 512 140 512 256c0 141.4-114.6 256-256 256S0 397.4 0 256C0 140 77.1 42.1 182.9 10.6c16.9-5 34.8 4.6 39.8 21.5z"/>
      </svg>
      Submitting Form...
    `
});

const error_div = document.createElement('div');
error_div.className = 'bg-red-600 text-white p-4 mb-4 rounded text-center';
/*--------------------------------------------------------------
FORM SUBMIT
--------------------------------------------------------------*/
function setFormElementsDisabled(disabled) {
    document.querySelectorAll('textarea, input, select, button').forEach(element => {
        element.disabled = disabled;
    });
}

document.addEventListener("DOMContentLoaded", function() {
    add_form.addEventListener("submit", function(e) {
        e.preventDefault();
        document.querySelectorAll('.border-red').forEach(function(element) {
            element.classList.remove('border-red');
        });
        document.querySelectorAll('.error-message').forEach(function(element) {
            element.remove();
        });
        if (error_div) { error_div.remove(); }

        var count_error = 0;

        add_form.querySelectorAll('textarea, select').forEach(function(element) {
            if (
                (element.tagName.toLowerCase() === 'textarea' && 
                 !element.classList.contains('explanation') && 
                 element.value.trim() === '') ||
                (element.tagName.toLowerCase() === 'select' && 
                 element.value === '')
            ) {
                element.classList.add('border-red');
                count_error++;
            }
        });
        
        //AT LEAST 1 ANSWER IS SELECTED ON MULTIPLE CHOICE AND/OR CHECKBOXES
        if(count_error == 0){
            add_form.querySelectorAll('.question-form').forEach(function(formElement, index) {
                var qtype = formElement.querySelector('.question-type').value;
                if (qtype !== "written") {
                    var checked = formElement.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked').length;
                    if (checked === 0) {
                        formElement.classList.add('border-red');

                        var answerDiv = formElement.querySelector('[answer-div]');
                        var errorMessage = document.createElement('i');

                        errorMessage.classList.add('error-message');
                        errorMessage.textContent = 'Please select an answer';

                        answerDiv.insertAdjacentElement('afterend', errorMessage);
                        count_error++;
                    }
                }
            });
        }

        if(count_error == 0){
            add_form.querySelectorAll('.question-form').forEach(function(formElement, index) {
                var count = index + 1;
                formElement.querySelector('.question').name = 'question-' + count;
                formElement.querySelector('.question-type').name = 'type-' + count;
                //This applies even to hidden elements
                var explanationElement = formElement.querySelector('.explanation');
                if (explanationElement) {
                    explanationElement.name = 'explanation-'+ count ;
                }
                
                var qtype = formElement.querySelector('[name="type-' + count + '"]').value;
                if (qtype === "written") {
                    formElement.querySelectorAll('.option-con').forEach(function(optionElement, i) {
                        var input = optionElement.querySelector('.regex');
                        input.name = "option-"+ count+ "-" + (i + 1);
                    });
                } else {
                    formElement.querySelectorAll('.option-con').forEach(function(optionElement, i) {
                        var input = optionElement.querySelector('input');
                        input.name = "answer-"+ count; //Boolean by default

                        if (qtype === "multiple-choice" || qtype === "check-box"){
                            var textarea = optionElement.querySelector('textarea');
                            var option = optionElement.querySelector('.option').value;

                            textarea.name = "option-"+ count+ "-" + (i + 1);
                            input.name = "answer-"+ count+ "-" + (i + 1);
                            input.value = option;
                        }
                    });
                }
            });
        }

        if(count_error == 0){
            let form_data = new FormData(add_form);
            let data_object = {};
            data_object['fullUrl'] = window.location.href;
            for (let [name, value] of form_data.entries()) {
                data_object[name] = value;
            }

            setFormElementsDisabled(true);
            body.prepend(loader);
            fetch('/quiz/add_quiz_form', {
                method: "POST",
                headers: {
                    'X-My-Custom-Header': 'fetch-request',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ data_object: data_object })
            })
            .then(response => response.json())
            .then(data => {
                setFormElementsDisabled(false);
                if(data.status == "success"){
                    console.log(data.message);
                    setTimeout(() => {
                        loader.innerHTML = 'Quiz Form Created Successfully...';
                    }, 1000);

                    setTimeout(() => {
                        loader.innerHTML = 'Redirecting...';
                    }, 1000);

                    setTimeout(() => {
                        window.location.replace(`${base_url}quiz`);
                    }, 2000);
                }else if(data.status == "validation"){
                    console.log(JSON.stringify(data.errors));
                    Object.keys(data.errors).forEach(function(index) {
                        var value = data.errors[index];
                        var inputElement = add_form.querySelector("[name='" + index + "']");
                        
                        if (inputElement) {
                            inputElement.classList.add("border-red");

                            var validation_error = document.createElement("div");
                            validation_error.className = "error-message";
                            validation_error.textContent = value;
                            inputElement.insertAdjacentElement('afterend',validation_error);
                        }
                    });
                    if (loader) { loader.remove(); }
                }else{
                    console.log(data.error);
                    error_div.innerHTML = data.message;
                    add_form.prepend(error_div);

                    if (loader) { loader.remove(); }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                setFormElementsDisabled(false);
                if (loader) { loader.remove(); }
            });
        }
    });
});