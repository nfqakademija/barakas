import React from 'react';
import ReactDOM from 'react-dom';
import Message from './message';
import Moment from 'moment';
class App extends React.Component {

    constructor()
    {
        super();

        this.state = {
            messages: [],
        };
    }

    componentDidMount()
    {
        const url = new URL('http://127.0.0.1:9090/.well-known/mercure');
        url.searchParams.append('topic', '/dormitory');
        const eventSource = new EventSource(url);
        eventSource.onmessage = e => {
            const data = JSON.parse(e.data);
            this.setState({
                messages: [data].concat(this.state.messages),
            });
            console.log(this.state.messages);
        };
    }
    render()
    {
        Moment.locale('lt');
        return (
            <div>
                {this.state.messages.map((messages) => (
                    <Message
                    key={messages.id}
                    id={messages.id}
                    user={messages.user}
                    roomNr={messages.roomNr}
                    content={messages.content}
                    createdAt={Moment(messages.createdAt).format('YYYY-MM-DD HH:mm:ss')}
                    />
                        ))}
            </div>
                )
    }
}
ReactDOM.render(<App/>, document.getElementById('list'));