import PropTypes from 'prop-types';
import React, { Component } from 'react'
import { DragSource } from 'react-dnd';
import BigCalendar from 'react-big-calendar'
//import Chip from 'material-ui/Chip';


/* drag sources */
let eventSource = {
  beginDrag(props) {
      console.log(props.event);
    return Object.assign({},
      {event: props.event},

      {anchor: 'drop'}
    )
  }
}

function collectSource(connect, monitor) {
    console.log('33');
  return {
    connectDragSource: connect.dragSource(),
    isDragging: monitor.isDragging()
  };
}

const propTypes = {
  connectDragSource: PropTypes.func.isRequired,
  isDragging: PropTypes.bool.isRequired,
  event: PropTypes.object.isRequired
}

class DraggableSidebarEvent extends Component {


  render() {
    let {connectDragSource, isDragging, event} = this.props;
    let EventWrapper = BigCalendar.components.eventWrapper;
    let {name} = event;


    return (
      <EventWrapper event={event}>
        {connectDragSource(<div style={{opacity: isDragging ? 0.5 : 1}}>
          <div onClick={()=>{this.props.onClickEvent(event)}}
          >{name}</div>
        </div>)}
      </EventWrapper>
    );
  }
}

DraggableSidebarEvent.propTypes = propTypes;


export default DragSource('event', eventSource, collectSource)(DraggableSidebarEvent);
