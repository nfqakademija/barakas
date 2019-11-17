const requestForm = $("#requestForm").hide();
const requestBtn = $("#requestBtn");
const closeRequestFormBtn = $("#closeRequestFormBtn");

requestBtn.click(() => {
    requestForm.slideToggle('fast');
});

closeRequestFormBtn.click(() => {
    requestForm.slideToggle('fast');
});
