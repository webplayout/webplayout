import React, { Component } from "react";
import Calendar from "react-big-calendar";
import moment from "moment";
import withDragAndDrop from "react-big-calendar/lib/addons/dragAndDrop";

import { DndProvider } from 'react-dnd'
import HTML5Backend from 'react-dnd-html5-backend'

import axios from 'axios'

//import "./App.css";
import "react-big-calendar/lib/addons/dragAndDrop/styles.css";
import "react-big-calendar/lib/css/react-big-calendar.css";

const localizer = Calendar.momentLocalizer(moment);
const DnDCalendar = withDragAndDrop(Calendar);

class Schedule extends Component {
  state = {
    events: [
      {
        start: new Date(),
        end: new Date(moment().add(1, "days")),
        title: "Some title"
      }
    ]
  };

  formats = {
        timeGutterFormat: 'HH:mm',

      //   timeRangeFormat: ({ start, end }, culture, local) => {
      //     local.format(start, 'HH:mm', culture) +
      //     ' — ' +
      //     local.format(end, inSame12Hr(start, end) ? 'HH:mm' : 'HH:mm', culture)
      // },

        eventTimeRangeStartFormat: ({ start, end }, culture, local) => {
            console.log('tt')
            local.format(end, 'HH:mm', culture) + ' — '},

        eventTimeRangeEndFormat: ({ start, end }, culture, local) => {
            ' — ' + local.format(end, 'HH:mm', culture)
        }
  };

  onEventResize = (type, { event, start, end, allDay }) => {
    this.setState(state => {
      state.events[0].start = start;
      state.events[0].end = end;
      return { events: state.events };
    });
  };

  onEventDrop = ({ event, start, end, allDay }) => {

    const updated = {
          start: moment(start).format(),
          end: moment(end).format()
    };

    axios.patch(`/schedules/` + event.id, updated)
        .then(res => {

            const updatedEvents = this.state.events;

            updatedEvents.map((item) => {
                if(item.id == event.id)
                {
                    item.start = new Date(updated.start)
                    item.end = new Date(updated.end)
                }
            })

            this.setState({ events: updatedEvents });
        })

  };

  componentDidMount() {

      axios.get(`/files/`)
          .then(res => {
              const items = res.data._embedded.items;
              console.log(res.data._embedded.items);
              //this.setState({ calendarEvents: items });
          })

    axios.get(`/schedules/`)
        .then(res => {
            const events = res.data._embedded.items.map(function(event, index){
                event.start = new Date(event.start)
                event.end = new Date(event.end)
                return event
            });

            this.setState({ events: events });
        })
  }

  render() {
    return (
      <div className="App">
      123
        <DnDCalendar
          defaultDate={new Date()}
          defaultView="week"
          events={this.state.events}
          localizer={localizer}
          formats={this.formats}
          onEventDrop={this.onEventDrop}
          onEventResize={this.onEventResize}
          resizable
          style={{ height: "100vh" }}
        />
      </div>
    );
  }
}

export default Schedule;
