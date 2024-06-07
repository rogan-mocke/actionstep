// utils.js

function toggleLoader(show) {
    $('.cssloader-overlay, .cssloader-container').toggle(show);
}

function handlePost(url, data, callback) {
    toggleLoader(true);
    $.post(url, data).done(function(resp) {
        callback(resp);
        toggleLoader(false);
    });
}

function formatJson(jsonString, targetElement) {
    const obj = JSON.parse(jsonString);
    const formattedJson = JSON.stringify(obj, null, 2);
    targetElement.html(`<pre>${formattedJson}</pre>`);
}
