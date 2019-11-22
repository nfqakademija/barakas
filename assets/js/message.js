import React from 'react';

class message extends React.Component {
    constructor(props)
    {
        super(props);

    }

    render()
    {
        const {id,createdAt,roomNr,content, user} = this.props;

        return (
            <ul className="list-unstyled">
                <li style={{fontSize: '14px'}}>
                    <img src="https://image.flaticon.com/icons/svg/785/785116.svg" style={{height: '17px'}} />
                    <strong>{user}</strong> <small><i>{createdAt}</i></small>
                    <span className="float-right">
            <img src="https://image.flaticon.com/icons/svg/1229/1229996.svg" style={{height: '25px'}} />
                        {roomNr}
          </span>
                    <br />
                    <div style={{width: '82%', paddingLeft: '3%'}}>{content}</div>
                    <br />
                    <small>

            <span className="help-button mr-2">
                <a href={'/dormitory/help/' + id} className="help-button">
              <img src="https://image.flaticon.com/icons/svg/1264/1264878.svg" /> Padėti
                    </a>
            </span>
                    </small>
                    <small>
            <span className="report-button">
              <img src="https://image.flaticon.com/icons/svg/196/196777.svg" /> Pranešti
            </span>
                    </small>
                </li>
                <hr />
            </ul>
        )

    }
}

export  default  message;