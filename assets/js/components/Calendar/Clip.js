import React from 'react'

const propTypes = {}

export default class Clip extends React.Component {
    constructor(props){
        super(props)
    }
    render() {
            return (<div
          style={{
            border: '2px solid gray',
            borderRadius: '4px',
            width: '100px',
            margin: '10px',
            textAlign: "center"
          }}
          className="justify-content-center align-self-center"
          draggable="true"
          key={this.props.item.id}
          onDragStart={this.props.onDragStart}
        >
          {this.props.item.name} {this.props.item.duration}
        </div>
        )
    }
}
