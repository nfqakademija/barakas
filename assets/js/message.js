import React from 'react';
import Fade from 'react-reveal/Fade';
class message extends React.Component {
    constructor(props)
    {
        super(props);
    }

    render()
    {
        const {id,createdAt,roomNr,content, user, status, currentUser, userId, solved} = this.props;

        return (
            <Fade>
                <li style={{fontSize: '14px'}}>
                    {status===0 ?  <img src="https://image.flaticon.com/icons/svg/785/785116.svg" style={{height: '17px'}} /> : null}

                    <strong>{user}</strong> <small><i>{createdAt}</i></small>
                    <span className="float-right">
            <img src="https://image.flaticon.com/icons/svg/1229/1229996.svg" style={{height: '25px'}} />
                        {roomNr}
          </span>
                    <br />
                    <div style={{width: '82%', paddingLeft: '3%'}}>{content}</div>
                    <br />
                    {userId===currentUser && solved===0 ?

                        <div>
                            <small>
                        <span className="isDisabled help-button mr-2">
                        <img src="https://image.flaticon.com/icons/svg/1264/1264878.svg"/>
                        <span>Padėti</span>
                        </span>
                            </small>
                            <small>
                        <span className="isDisabled report-button">
                        <img src="https://image.flaticon.com/icons/svg/196/196777.svg"/>
                        <span>Pranešti</span>
                        </span>
                            </small>
                        </div>
                        : solved===1 ?
                            <small>
                        <span style={{cursor: 'default'}} className="help-button">
                        <img src="https://image.flaticon.com/icons/svg/443/443138.svg" />
                        <span>Pagalba suteikta</span>
                        </span>
                            </small>
                        :
                            <div>
                                <small>
                                    <a href={'/dormitory/help/'+id}>
                        <span className="help-button mr-2">
                        <img src="https://image.flaticon.com/icons/svg/1264/1264878.svg" /> Padėti
                        </span>
                                </a>
                                </small>
                                <small>
                        <span className="report-button">
                        <img src="https://image.flaticon.com/icons/svg/196/196777.svg" /> Pranešti
                        </span>
                                </small>
                            </div>
                    }
                    <hr />
                </li>
            </Fade>
        )

    }
}

export  default  message;