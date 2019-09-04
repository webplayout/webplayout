import React, {Component} from 'react';
import ScheduleEvent from './ScheduleEvent'


export default class Sidebar extends React.Component {

  render() {

    const eventList = !!this.props.events ? this.props.events.map((event) => {
      return (<ScheduleEvent
          event={event}
          key={event.id}
          onClickEvent={this.props.onClickEvent}
        />
      )
    }) : null

    return (
      <div>
        {eventList}
      </div>
    );
  }
}
