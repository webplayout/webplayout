import React from 'react'

import axios from 'axios'

const propTypes = {}

class ClipList extends React.Component {

    state = {
        clips: []
    };

    componentDidMount() {

        axios.get(`/files/`)
            .then(res => {
                const items = res.data._embedded.items;
                this.setState({ clips: items });
            })
    };

    render() {
        const eventList = !!this.state.clips ? this.state.clips.map((event) => {
          return (<div
              key={event.id}

              draggable="true"
              onDragStart={() =>
                this.handleDragStart({ title: name, name })
              }
            >{event.name}</div>
        )}):null

  return (
    <div>
      {eventList}
    </div>
)
}
}

ClipList.propTypes = propTypes

export default ClipList
