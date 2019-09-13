import React from 'react'
import { Calendar, Views } from 'react-big-calendar'
import withDragAndDrop from 'react-big-calendar/lib/addons/dragAndDrop'

import PropTypes, { element } from 'prop-types'

import moment from "moment";

import Card from './Card'
import Clip from './Clip'

//import css
import 'react-big-calendar/lib/css/react-big-calendar.css'
import 'react-big-calendar/lib/addons/dragAndDrop/styles.css'
import './calendar.css'

//declare gobal functions
const DragAndDropCalendar = withDragAndDrop(Calendar)
const formatName = (name, count) => `${name}  ${count}`

import axios from 'axios'
import InfiniteScroll from 'react-infinite-scroller'

class MyEvent extends React.Component {
    constructor(props){
        super(props)
    }
    componentDidMount(){
        //MyGlobal.popOver();
    }
    render(){
        return (
        <div>
            <div
                    className="custom_event_content"
                    data-toggle="popover"
                    data-placement="top"
                    data-popover-content={"#custom_event_" + this.props.event.id}
                    tabIndex="0"
                    >
                {this.props.event.title}
            </div>

            <div className="hidden" id={"custom_event_" + this.props.event.id} >
              <div className="popover-heading">
                {this.props.event.driver}
              </div>

              <div className="popover-body">
                {this.props.event.title}<br/>
              </div>
            </div>
        </div>
        );
    }
}

class Dnd extends React.Component {

  static propTypes = {
    id: PropTypes.string,
    // firestore: PropTypes.shape({
    //   add: PropTypes.func.isRequired
    // }).isRequired
  }

  constructor(props) {
    super(props)
    this.state = {
      //events: events,
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

    this.handleDisplayDragItemInCell = this.handleDisplayDragItemInCell.bind(this);
    this.handleDragStart = this.handleDragStart.bind(this);
    this.dragFromOutsideItem = this.dragFromOutsideItem.bind(this);
    this.customOnDragOver = this.customOnDragOver.bind(this);
    this.onDropFromOutside = this.onDropFromOutside.bind(this);
    this.moveEvent = this.moveEvent.bind(this);
    this.resizeEvent = this.resizeEvent.bind(this);
    this.newEvent = this.newEvent.bind(this);

    this.handleClickOpen = this.handleClickOpen.bind(this);
    this.handleClose = this.handleClose.bind(this);
    this.handleSave = this.handleSave.bind(this);

    this.handleNameTextFieldChange = this.handleNameTextFieldChange.bind(this);
    this.handleIdTextFieldChange = this.handleIdTextFieldChange.bind(this);
  }

  componentWillReceiveProps(nextProps) {
    var counters = {};
    if (nextProps.elements) {
      nextProps.elements.map((element, index) => {
        console.log("element: ", element);
        counters[element.element_name] = index;
      })
    }
    console.log("here is conunters: ", counters);
    this.setState({
      counters: counters
    })
  }

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

  onDropFromOutside = ({ start, end, allDay }) => {

    const { draggedEvent, counters } = this.state

    const event = {
      title: draggedEvent.title,
      start,
      end: new Date(moment(start).add(draggedEvent.duration, 'seconds').format()),
      file: draggedEvent.file
    }

    this.setState({ draggedEvent: null})
    this.newEvent(event)
  }

  moveEvent({ event, start, end, isAllDay: droppedOnAllDaySlot }) {
    const { events } = this.state
    const updatedEvent = { start, end }

    axios.patch(`/schedules/` + event.id, updatedEvent)
        .then(res => {

            events.map((item) => {
                if(item.id == event.id)
                {
                    item.start = new Date(updatedEvent.start)
                    item.end = new Date(updatedEvent.end)
                }
            })

            this.setState({ events: events });
        })
  }

  resizeEvent = ({ event, start, end }) => {
      return false;
    const { events } = this.state

    const nextEvents = events.map(existingEvent => {
      return existingEvent.id == event.id
        ? { ...existingEvent, start, end }
        : existingEvent
    })

    this.setState({
      events: nextEvents,
    })

    //alert(`${event.title} was resized to ${start}-${end}`)
  }

  newEvent(event) {
    let idList = this.state.events.map(a => a.id)
    //let newId = Math.max(...idList) + 1
    let hour = {
      //id: newId,
      title: event.title,
      //allDay: event.isAllDay,
      start: new Date(event.start),
      end: new Date(event.end),
      file: event.file
      // 'date[year]': '2019',
      // 'date[month]': '10',
      // 'date[day]': '2'
    }


    axios.post('/schedules/', hour)
     .then(response => {

        hour.id = response.data.id;

        this.setState({
            events: this.state.events.concat([hour]),
        });
     })


  }

  handleClickOpen = () => {
    this.setState({
      dialogOpen: true,
    });
  }

  handleClose = () => {
    this.setState({
      dialogOpen: false,
      element_id: '',
      element_name: ''
    });
  }

  handleSave = () => {
    this.props.addElement(this.state.element_name);
    this.handleClose();
  }

  handleNameTextFieldChange = ($e) => {
    this.setState({
      element_name: $e.target.value
    })
  }

  handleIdTextFieldChange = ($e) => {
    this.setState({
      element_id: $e.target.value
    })
  }

  showDelete = () => {

  }

  selectedEvent = (event, e) => {

  }

  deleteEvent = (event, e) => {

      axios.delete(`/schedules/` + event.id)
          .then(res => {
              const events = this.state.events.filter(function(item) {
                  return item !== event
              });

              this.setState({events: events});
          })
  }

  componentDidMount() {
this.loadMore();


      axios.get(`/schedules/?limit=10000`)
          .then(res => {
              const items = res.data._embedded.items;

              const events = items.map((item) => {

                      item.start = new Date(item.start)
                      item.end = new Date(item.end)
                  return item;
              });

              //console.log(res.data._embedded.items);
              this.setState({ events: events });
          })
  };

  loadMore = () => {

      return axios.get(`/files/?page=` + (this.state.page+1))
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
  componentWillMount(){
      this.setState({height: window.innerHeight + 'px'});
    }

  render() {

      const components = {
         // event: MyEvent
      }


    return (
      <div>
        <div className="" style={{ display: 'flex', position:'fixed', width:'200px' }}>

          <div
            style={{
              display: 'flex',
              flex: 1,
              justifyContent: 'center',
              flexWrap: 'wrap',
              height: this.state.height,
              overflowY: 'scroll',
            }}
          >
          <InfiniteScroll
              pageStart={0}
              loadMore={this.loadMore}
              hasMore={this.state.pages > this.state.page}
              loader={<div className="loader" key={0}>Loading ...</div>}
              useWindow={false}
          >{this.state.pageItems}
          </InfiniteScroll>
          </div>
        </div>
        <DragAndDropCalendar style={{marginLeft:'200px'}}
          components={components}
          selectable
          localizer={this.props.localizer}
          events={this.state.events}
          onEventDrop={this.moveEvent}
          dragFromOutsideItem={
            this.state.displayDragItemInCell ? this.dragFromOutsideItem : null
          }
          onDropFromOutside={this.onDropFromOutside}
          onDragOver={this.customOnDragOver}

          defaultView="week"
          defaultDate={new Date()}
          onSelectSlot={this.showDelete}
          onSelectEvent={this.selectedEvent}
          onDoubleClickEvent={this.deleteEvent}
        />
      </div>
    )
  }
}

export default Dnd;
