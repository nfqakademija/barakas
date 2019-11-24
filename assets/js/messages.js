import React from 'react';
import ReactDOM from 'react-dom';
import Message from './message';
import Moment from 'moment';
import axios from 'axios';
class App extends React.Component {

    constructor()
    {
        super();

        this.state = {
            messages: [],
            user: []
        };

    }

    componentDidMount()
    {
        axios.get(`http://127.0.0.1:8000/dormitory/get/messages`)
            .then(res => {
                this.setState({
                    messages: res.data.data,
                    user: res.data.user_id
                });
            });

        const url = new URL('http://127.0.0.1:9090/.well-known/mercure');
        url.searchParams.append('topic', 'http://127.0.0.1:8000/dormitory');
        const eventSource = new EventSource(url);
        eventSource.onmessage = e => {
            const data = JSON.parse(e.data);
            this.setState({
                messages: [data].concat(this.state.messages),
            });
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
                    userId = {messages.userId}
                    status = {messages.status}
                    solved = {messages.solved}
                    user={messages.user}
                    roomNr={messages.roomNr}
                    content={messages.content}
                    createdAt={Moment(messages.createdAt).format('YYYY-MM-DD HH:mm:ss')}
                    currentUser = {this.state.user}
                    />
                        ))}
            </div>
                )
    }
}
ReactDOM.render(<App/>, document.getElementById('list'));