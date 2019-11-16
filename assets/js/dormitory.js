const requestForm = $("#requestForm").hide();
const requestBtn = $("#requestBtn");

requestBtn.click(() => {
    requestForm.slideToggle('fast');
});
