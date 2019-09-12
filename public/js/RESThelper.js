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