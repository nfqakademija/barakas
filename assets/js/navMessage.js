const link = $("#navMessage").data('mercureLink');
const u = new URL(link);
u.searchParams.append('topic', encodeURI(window.location.href));
const es = new EventSource(u);
es.onmessage = e => {
    const danger = $("#navCount");
    const text =parseInt(danger.text());
    if (isNaN(text)) {
        danger.text(1);
    } else {
        danger.text(text+1);
    }
    const data = JSON.parse(e.data);
    const nav = '<li>\n' +
        '<small>\n' +
        '<a href="/dormitory/message/'+data.id+'" class="notification-url">\n' +
        data.owner +' | Kambarys: <strong>'+data.room+'</strong> <br>\n' +
        '<strong>Prašymas:</strong> '+data.content+'\n' +
        '<br>\n' +
        '<i>dabar</i>\n' +
        '</a>\n' +
        '</small>\n' +
        '<hr>\n' +
        '</li>';
    $("#append").before(nav);
}

