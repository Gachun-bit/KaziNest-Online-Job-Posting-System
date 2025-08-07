document.addEventListener("DOMContentLoaded", function () {
    window.showform = function (formId) {
        document.querySelectorAll(".Register").forEach(form => form.classList.remove("active"));
        document.getElementById(formId).classList.add("active");
    };
});
