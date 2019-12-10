const u = new URL('http://127.0.0.1:9090/.well-known/mercure');
u.searchParams.append('topic', window.location.href);
const es = new EventSource(u);
es.onmessage = e => {
    const ul = document.getElementById("message");
    console.log(JSON.parse(e.data));
}

