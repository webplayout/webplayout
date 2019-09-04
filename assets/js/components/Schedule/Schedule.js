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
const DnDCalendar = withDragAndDrop(Calendar, {backend: false});

import Sidebar from './ScheduleSidebar'

class Schedule extends Component {

    constructor(props) {
      super(props)

      //this.state = calendarInitialState

      this.onEventDrop = this.onEventDrop.bind(this)
      this.onClipDrop = this.onClipDrop.bind(this)

    }

  state = {
      draggedEvent: null,
    clips: [],
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

  onEventResize = ({ event, start, end }) => {
    console.log('Not supported');
    // this.setState(state => {
    //   state.events[0].start = start;
    //   state.events[0].end = end;
    //   return { events: state.events };
    // });
  };

  onEventDrop = ({ event, start, end, allDay }) => {
    console.log('onEventDrop');
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

  onClipDrop = ({ start, end, allDay }) => {
console.log('drop')
  };

  componentDidMount() {

      axios.get(`/files/`)
          .then(res => {
              const items = res.data._embedded.items;
              console.log(res.data._embedded.items);
              this.setState({ clips: items });
          })

    axios.get(`/schedules/`)
        .then(res => {
            //const events = res.data._embedded.items;
            const events = res.data._embedded.items.map(function(event, index){
                console.log(event);
                event.start = new Date(event.start)
                event.end = new Date(event.end)
                event.allDay = false;
                return event
            });

            this.setState({ events: events });
        })
  }

handleClips()
{
console.log('handleClips');
};


handleDragStart = event => {
    this.setState({ draggedEvent: event })
  }

  handleDisplayDragItemInCell = () => {
    this.setState({
      displayDragItemInCell: !this.state.displayDragItemInCell,
    })
  }

  dragFromOutsideItem = () => {
    return this.state.draggedEvent
  }

customOnDragOver = event => {
   // check for undroppable is specific to this example
   // and not part of API. This just demonstrates that
   // onDragOver can optionally be passed to conditionally
   // allow draggable items to be dropped on cal, based on
   // whether event.preventDefault is called
   if (this.state.draggedEvent !== 'undroppable') {
     console.log('preventDefault')
     event.preventDefault()
   }
 }

  render() {
    return (
      <div className="App">
        <DndProvider backend={HTML5Backend}>

            <Sidebar events={this.state.clips}
                onClickEvent={this.handleClips}
            />

            <DnDCalendar
              defaultDate={new Date()}
              defaultView="week"
              events={this.state.events}
              localizer={localizer}
              formats={this.formats}
              onEventDrop={this.onEventDrop}
              onDropFromOutside={this.onClipDrop}
              onDragOver={this.customOnDragOver}
              onEventResize={this.onEventResize}
              resizable
              style={{ height: "100vh" }}
            />

        </DndProvider>
      </div>
    );
  }
}

export default Schedule;

//export default DndProvider(HTML5Backend)(Schedule)
