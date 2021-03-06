import React from 'react'

const propTypes = {};

export default class Clip extends React.Component {
    state = {}
    constructor(props){
        super(props)

        const item = JSON.stringify({
            title: this.props.item.name,
            duration: {seconds: this.props.item.duration},
            file: this.props.item.id,
        })

        this.state.item = item;
    }
    render() {
        return (<div data-event={this.state.item}
          className="card p-0 m-1"
          draggable="true"
          key={this.props.item.id}
          onDragStart={this.props.onDragStart}
        >
            <div className="card-body p-1 m-0">
                <p className="card-text text-primary small text-truncate">

                    {this.props.item.name}<br />

                    <small className="text-muted">
                        {this.props.item.type == 'clip' ?
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-film"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>
                        :
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                        }
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        &nbsp;{this.props.item.duration}
                    </small>
                </p>
              </div>
        </div>
        )
    }
}
