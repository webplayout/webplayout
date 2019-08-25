import React from 'react'
import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction' // needed for dayClick

import axios from 'axios'
//import './main.scss'
import '../css/scheduler.scss'

export default class SchedulerApp extends React.Component {

  calendarComponentRef = React.createRef()

  state = {
    calendarWeekends: true,
    calendarEvents: []
  }

  componentDidMount() {

      axios.get(`/files/`)
          .then(res => {
              const items = res.data._embedded.items;
              //console.log(res.data._embedded.items);
              //this.setState({ calendarEvents: items });
          })

    axios.get(`/schedules/`)
        .then(res => {
            const items = res.data._embedded.items;
            //console.log(res.data._embedded.items);
            this.setState({ calendarEvents: items });
        })
  }

  render() {
    return (
      <div className='demo-app'>
        <div className='demo-app-top'>

        </div>
        <div className='demo-app-calendar'>

        <div id='external-events'>
          <h4>Draggable Events</h4>

          <div id='external-events-list'>
            <div className='fc-event'>My Event 1</div>
            <div className='fc-event'>My Event 2</div>
            <div className='fc-event'>My Event 3</div>
            <div className='fc-event'>My Event 4</div>
            <div className='fc-event'>My Event 5</div>
          </div>

          <p>
            <input type='checkbox' id='drop-remove' />
            <label htmlFor='drop-remove'>remove after drop</label>
          </p>
        </div>

          <FullCalendar
            defaultView="timeGridWeek"
            header={{
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            }}
            plugins={[ dayGridPlugin, timeGridPlugin, interactionPlugin ]}
            ref={ this.calendarComponentRef }
            events={ this.state.calendarEvents }
            dateClick={ this.handleDateClick }
            nowIndicator="true"
            allDaySlot="false"
            editable="false"
            droppable="true"
            drop={ this.drop }
            onChange={ this.onChange }
            slotDuration="00:05:00"
            />
        </div>
      </div>
    )
  }

  drop = () => {
      console.log('drop')
  }

  onChange = () => {
      console.log('onChange')
  }

  toggleWeekends = () => {
    this.setState({ // update a property
      calendarWeekends: !this.state.calendarWeekends
    })
  }

  handleDateClick = (arg) => {

console.log(arg);
return;
      axios.post('/schedules/', {
        title: '1123132',
        start: '2019-07-27T11:00:00+03:00',
        end: '2019-07-27T12:00:00+03:00'
      })
           .then(response => console.log(response))

// this.setState(prevState => ({
//     array: [...prevState.array, newElement]
// }))


      // axios.get('https://api.github.com/users/maecapozzi')
      //     .then(response => console.log(response))

    console.log(arg.dateStr);
    // if (confirm('Would you like to add an event to ' + arg.dateStr + ' ?')) {
    //   this.setState({  // add new event data
    //     calendarEvents: this.state.calendarEvents.concat({ // creates a new array
    //       title: 'New Event',
    //       start: arg.date,
    //       allDay: arg.allDay
    //     })
    //   })
    // }
  }

}
