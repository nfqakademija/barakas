const u = new URL('http://127.0.0.1:9090/.well-known/mercure');
u.searchParams.append('topic', window.location.href);
const es = new EventSource(u);
es.onmessage = e => {
    const ul = $("#realmessage");
    const data = JSON.parse(e.data)
    const html = '<li style="font-size: 17px; margin-left: 1%">\n' +
        '            <div class="float-left">\n' +
        '                                    <img src="https://image.flaticon.com/icons/png/128/753/753345.png" style="height: 25px">\n' +
        '                            </div>\n' +
        '\n' +
        '            <div class="messageContext">\n' +
        '                <strong><span>'+data.owner+'</span></strong> <small><i>dabar</i></small>\n' +
        '                            <div style="padding-top: 2%">'+data.content+'</div>\n' +
        '            <div style="margin-top: -5%;">\n' +
        '                <br>\n' +
        '                            <br>\n' +
        '                <small>\n' +
        '                    <a href="/dormitory/help/'+data.id+'" class="btn help-button">\n' +
        '                        <img src="https://image.flaticon.com/icons/svg/1264/1264878.svg">\n' +
        '                        <span>Padėti</span>\n' +
        '                    </a>\n' +
        '                </small>\n' +
        '                                &nbsp;\n' +
        '                                    <small>\n' +
        '                        <a href="/dormitory/report/message?id='+data.id+'" class="btn report-button">\n' +
        '                            <img src="https://image.flaticon.com/icons/svg/196/196777.svg">\n' +
        '                            <span>Pranešti</span>\n' +
        '                        </a>\n' +
        '                    </small>\n' +
        '                                        </div>\n' +
        '            </div>\n' +
        '            <hr>\n' +
        '        </li>';
    ul.prepend(html);
    console.log(JSON.parse(e.data));
    console.log(ul);
}

