import React from 'react'

import axios from 'axios'

import InfiniteScroll from 'react-infinite-scroller'

import FullCalendar from '@fullcalendar/react'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin, {Draggable} from '@fullcalendar/interaction' // needed for dayClick

import '@fullcalendar/core/main.css';
import '@fullcalendar/bootstrap/main.css';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';

import './style.css'
import Clip from './Clip.js'

export default class SchedulerApp extends React.Component {

    calendarComponentRef = React.createRef()

    state = {
        events: []
    }

    constructor(props) {
        super(props)
        this.state = {
            events: [],
            draggedEvent: null,
            counters: {},
            elements: null,
            displayDragItemInCell: true,
            dialogOpen: false,
            element_id: '',
            element_name: '',
            page: 0,
            pages: 0,
            total:0,
            pageItems: []
        }
    }

    componentDidMount() {
        var containerEl = document.getElementById('external-events');

        new Draggable(containerEl, {
            itemSelector: '.card'
        });

        this.loadMore();

        axios.get(`/schedules/?limit=10000`)
            .then(res => {
                const items = res.data._embedded.items;
                const events = items.map((item) => {
                    item.start = new Date(item.start)
                    item.end = new Date(item.end)
                    return item;
                });

                this.setState({ events: events });
            })
    }

    loadMore = () => {

      return axios.get(`/files/?sorting[type]=ASC&sorting[id]=DESC&page=` + (this.state.page+1))
          .then(res => {
              const clips = res.data._embedded.items;
              const items = [];
              clips.map((clip,i) => {
                  items.push(
                      <Clip key={clip.id} item={clip}
                      onDragStart={() =>
                        this.handleDragStart({ title: clip.name, duration: clip.duration, file: clip.id })
                      } />
                  )
              })

              this.setState({
                  pageItems: this.state.pageItems.concat(items),
                  pages: res.data.pages,
                  page: res.data.page,
                  total: res.data.total
               });
          })
    }

    onEventResize(info) {
        // resie is not allowed
        info.revert();
    }

    eventDrop(info) {
        const event = info.event

        axios.patch(`/schedules/` + event.id, { start: event.start, end: event.end })
        .then(res => {

        })
    }

    componentWillMount() {
        this.setState({height: (window.innerHeight-50) + 'px'});
    }

    deleteClassName = 'delete'

    eventRender = ({event, el}) => {
        var deleteEl = document.createElement('span');
        deleteEl.className = this.deleteClassName;
        deleteEl.innerText = 'X'
        el.append(deleteEl);
    }

    eventClick = ({el, event, jsEvent, view}) => {
        const currentEvent = event;
        const eventId = event.id;

        if (jsEvent.target.className === this.deleteClassName && typeof(eventId) !== 'undefined') {

            if (confirm('Are you sure you want to delete this event ' + eventId)) {
                axios.delete(`/schedules/` + eventId)
                    .then(res => {
                        console.log(this.calendarComponentRef.current.calendar.getEventById(eventId))

                    }).finally(() => {
                        currentEvent.remove()
                    })
            }
        }
    }

    eventReceive = ({draggedEl, event, view}) => {

    let postData = {
        title: event.title,
        //allDay: event.isAllDay,
        start: event.start,
        end: event.end,
        file: event.extendedProps.file
    }

    axios.post('/schedules/', postData)
        .then(response => {

            const updatedEvent = {
                id: response.data.id,
                title: event.title,
                start: event.start,
                end: event.end,
                file: response.data.file.id
            };

            event.remove();

            this.setState({events: [...this.state.events, updatedEvent]})
        }).finally(() => {

        })
    }

    render() {
        return (
        <div className="row">
            <div className="col-2 p-0">

              <div id='external-events'
                style={{
                  height: this.state.height,
                }}
              >
              <InfiniteScroll id='external-events-list'
                  pageStart={0}
                  loadMore={this.loadMore}
                  hasMore={this.state.pages > this.state.page}
                  loader={<div className="loader" key={0}>Loading ...</div>}
                  useWindow={false}
              >{this.state.pageItems}
              </InfiniteScroll>
              </div>
            </div>

            <div className="col-10">
              <FullCalendar
                defaultView="timeGridWeek"
                header={{
                  left: 'prev,next today',
                  center: 'title',
                  right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                }}
                plugins={[ dayGridPlugin, timeGridPlugin, interactionPlugin, 'bootstrap' ]}
                themeSystem="bootstrap"
                ref={ this.calendarComponentRef }
                events={ this.state.events }
                nowIndicator={true}
                allDaySlot={false}
                editable={true}
                droppable={true}
                eventResize={ this.onEventResize }
                eventDrop={ this.eventDrop }
                eventRender={this.eventRender}
                eventClick={this.eventClick}
                eventReceive={this.eventReceive}
                height="parent"
                eventTextColor="#fff"
                eventBackgroundColor="#3a87ad"
                snapDuration="00:01:00"
                />
                </div>
          </div>
        )
    }
}
