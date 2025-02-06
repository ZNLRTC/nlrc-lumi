function createElementFromHTML(htmlString) {
    const div = document.createElement('div');
    div.innerHTML = htmlString.trim();
    return div.firstChild;
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".form");
    /*--------------------------------------------------------------
    TEXTAREA AUTO ADJUST
    --------------------------------------------------------------*/
    function adjustTextareaHeight(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = `${textarea.scrollHeight}px`;
    }
    
    document.querySelectorAll('textarea').forEach(textarea => adjustTextareaHeight(textarea));
    
    document.addEventListener('input', e => {
        if (e.target.tagName.toLowerCase() === 'textarea') {
            adjustTextareaHeight(e.target);
        }

        //OPTION TEXTAREAS
        if (e.target.matches('.option-con textarea')) {
            var textarea = e.target;
            var inputElements = Array.from(textarea.parentNode.querySelectorAll('[type="checkbox"], [type="radio"]'));
            var hasValue = textarea.value.trim().length > 0;
            
            inputElements.forEach(function(input) {
                input.disabled = !hasValue;
                if (!hasValue) { input.checked = false; }
            });
        }
    });

    document.querySelectorAll("#quiz_form textarea").forEach(textarea =>
        textarea.dispatchEvent(new Event('input'))
    );
    /*--------------------------------------------------------------
    QUESTION ACTIONS
    --------------------------------------------------------------*/
    document.addEventListener("click", function(e) {
        var button = e.target.closest('button');
        //ADD QUESTION
        if (button && button.classList.contains('add-question')) {
            const formGroup = button.closest('.form-group');
            fetch('/quiz/add_section', {
                headers: {
                  'X-My-Custom-Header': 'fetch-request',
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                const newContent = createElementFromHTML(data.content);
                formGroup.after(newContent);

                const firstQuestionForm = form.querySelectorAll(".question-form");
                const first = firstQuestionForm[0];
                if (!first.querySelector(".remove-one")) {
                    const actionElement = createElementFromHTML(data.action);
                    first.querySelector(".form-action").prepend(actionElement);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        //DELETE QUESTION
        if (button && button.classList.contains('delete-question')) {
            button.closest('.form-group').remove();
            if (form.querySelectorAll('.question-form').length === 1) {
                document.querySelectorAll('.remove-one').forEach(element => element.remove());
            }
        }

        //RADIO BUTTONS
        if (e.target.type === 'radio') {
            var parent = e.target.closest('[answer-div]');
            var radios = parent.querySelectorAll('[type="radio"]');
            radios.forEach(function(radio) {
                if (radio !== e.target) { radio.checked = false; }
            });
        }

        //REMOVE OPTION
        if (button && button.classList.contains('remove-option')) {
            var thisElement = e.target;
            var parent = thisElement.closest('.question-form');
            var optionCon = thisElement.closest('.option-con');
            
            if (optionCon) { optionCon.remove(); }
            
            var optionConElements = parent.querySelectorAll('.option-con');
            if (optionConElements.length <= 2) {
                var removeOptionButtons = parent.querySelectorAll('.remove-option');
                removeOptionButtons.forEach(function(button) {
                    button.remove();
                });
            }
            
        }
        //ADD OPTION
        if (button && button.classList.contains('add-option')) {
            const formGroup = button.closest('.form-group');
            var div = formGroup.querySelector("[answer-div]");
            var option_type = 'radio';

            if(div.classList.contains('checkbox-div')){
                option_type = 'checkbox';
            }else if(div.classList.contains('written-div')){
                option_type = 'regex_field';
            }

            fetch('/quiz/add_option', {
                method: "POST",
                headers: {
                  'X-My-Custom-Header': 'fetch-request',
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ option_type: option_type })
            })
            .then(response => response.json())
            .then(data => {
                var optionCon = button.closest('.option-con');
                if (optionCon) {
                    optionCon.insertAdjacentHTML('afterend', data.content);
                }

                var optionConElements = formGroup.querySelectorAll('.option-con');
                var count = optionConElements.length;
                if ((option_type !== 'regex_field' && count > 2) || (option_type == 'regex_field' && count > 1)) {
                    var removeOptions = formGroup.querySelectorAll('.remove-option');
                    removeOptions.forEach(function(index) {
                        index.remove();
                    });

                    var optionActions = formGroup.querySelectorAll('.option-actions');
                    optionActions.forEach(function(index) {
                        index.insertAdjacentHTML('afterbegin', data.remove);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
    
    //QUESTION TYPES
    document.addEventListener("change",function(e){
        if (e.target.matches(".question-type")) {
            e.preventDefault();
            const qtype = e.target.value;
            const fgroup = e.target.closest(".form-group");
    
            if (qtype === "") {
                fgroup.querySelectorAll("[answer-div]").forEach(element => element.remove());
            } else {
                fetch('/quiz/question_type', {
                    method: "POST",
                    headers: {
                      'X-My-Custom-Header': 'fetch-request',
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ qtype: qtype })
                })
                .then(response => response.json())
                .then(data => {
                    fgroup.querySelectorAll("[answer-div]").forEach(element => element.remove());
                    
                    const formAction = fgroup.querySelector(".form-action");
                    if (formAction) {
                        formAction.insertAdjacentHTML('beforebegin', data.answer_div);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    });
});