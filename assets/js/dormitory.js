const requestForm = $("#requestForm").hide();
const requestBtn = $("#requestBtn");
const closeRequestFormBtn = $("#closeRequestFormBtn");

requestBtn.click(() => {
    requestForm.slideToggle('fast');
});

closeRequestFormBtn.click(() => {
    requestForm.slideToggle('fast');
});

$('#topStudents > li').first().prepend('<img src="https://image.flaticon.com/icons/svg/1021/1021220.svg" ' +
    'style="height: 25px; vertical-align: text-bottom">');
$('#topStudents > li:nth-child(2)').prepend('<img src="https://image.flaticon.com/icons/svg/179/179251.svg" ' +
    'style="height: 23px; vertical-align: text-bottom">');
$('#topStudents > li:nth-child(3)').prepend('<img src="https://image.flaticon.com/icons/svg/179/179250.svg" ' +
    'style="height: 23px; vertical-align: text-bottom">');