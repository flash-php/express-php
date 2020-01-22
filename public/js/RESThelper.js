// PUT and DELETE form handler
const forms = document.getElementsByTagName('form');
Array.from(forms).forEach(form => {

    const formMethod = form.getAttribute('method');
    
    if (formMethod !== 'GET')
        form.setAttribute('method', 'POST');

    if (formMethod === 'PUT')
        form.innerHTML += `<input type='hidden' name='REQUEST_METHOD' value='${formMethod}'>`;

    else if(formMethod === 'DELETE')
        form.innerHTML = `<input type='hidden' name='REQUEST_METHOD' value='${formMethod}'>`;
});


// GET, POST, PUT, DELETE anchor tags.
// <a href='/home/index' data-req='POST' data-body='{"name": "Ingo Andelhofs", "id": 5}'>Link to somewehere.</a>
const anchors = document.getElementsByTagName('a');
Array.from(anchors).forEach(anchor => {
    if (anchor.dataset.req && anchor.dataset.req !== 'GET' && anchor.dataset.async !== 'true') {
        anchor.addEventListener('click', e => {
            e.preventDefault();
            
            // Generate form.
            const generatedForm = document.createElement('form');
            generatedForm.action = anchor.href;
            generatedForm.method = 'POST';

            const requestMethod = document.createElement('input');
            requestMethod.type = 'hidden';
            requestMethod.name = 'REQUEST_METHOD';
            requestMethod.value = anchor.dataset.req;
            generatedForm.appendChild(requestMethod);

            // Generate hidden inputs.
            const json_body_entries = (anchor.dataset.req === 'DELETE') ? [] : Object.entries(JSON.parse(anchor.dataset.body));
            for(const [name, value] of json_body_entries) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = name;
                hiddenInput.value = value;
                generatedForm.appendChild(hiddenInput);
            }
            
            // Simulate form submit
            document.body.appendChild(generatedForm);
            generatedForm.submit();
        });
    }
});
