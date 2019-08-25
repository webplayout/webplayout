FROM debian:9

MAINTAINER Nikita Omarev <nikita@omarev.com>

RUN echo "deb http://deb.debian.org/debian stable main contrib non-free" >> /etc/apt/sources.list

RUN apt-get update && apt-get -y install figlet ant git wget mariadb-server \
  gnome-terminal build-essential checkinstall automake autoconf libtool \
  intltool yasm swig libmp3lame-dev libgavl-dev clang gcc g++ python2.7 \
  libsamplerate-dev libxml2-dev ladspa-sdk libjack-dev libsox-dev libsdl-dev \
  libgtk2.0-dev libsoup2.4-dev libqt4-dev libexif-dev libtheora-dev \
  libvdpau-dev libvorbis-dev python-dev tmux libpango1.0-0 libpangox-1.0-0 \
  libfdk-aac-dev libva-dev libsdl2-dev

RUN wget https://www.nasm.us/pub/nasm/releasebuilds/2.13/nasm-2.13.tar.bz2 \
  && tar xfj nasm-2.13.tar.bz2 \
  && cd nasm-2.13 \
  && ./autogen.sh \
  && ./configure --prefix=/usr/local/ \
  && make \
  && make install

# Generate config file for building melted
RUN echo "INSTALL_DIR=\"/usr/local\"" > build-melted.conf \
  && echo "SOURCE_DIR=\"/opt/melted\"" >> build-melted.conf \
  && echo "SOURCES_CLEAN=1" >> build-melted.conf \
  && echo "AUTO_APPEND_DATE=0" >> build-melted.conf \
  && echo "CREATE_STARTUP_SCRIPT=0" >> build-melted.conf \
  && echo "FFMPEG_SUPPORT_FAAC=0" >> build-melted.conf \
  && echo "ENABLE_FREI0R=0" >> build-melted.conf \
  && echo "FFMPEG_ADDITIONAL_OPTIONS=\"--enable-vaapi --enable-nonfree --enable-libfdk-aac\"" >> build-melted.conf

RUN wget https://raw.githubusercontent.com/mltframework/mlt-scripts/ad124dd6e8e378515533b1a243f2f81877516506/build/build-melted.sh

RUN chmod a+x build-melted.sh

RUN ./build-melted.sh -c build-melted.conf


RUN apt-get install -y i965-va-driver libvdpau-va-gl1 mesa-va-drivers vainfo

ENV PATH="/opt/melted/bin:${PATH}"
ENV MLT_PROFILE=atsc_1080p_25

RUN useradd -rm -G video -u 1000 melted

RUN mkdir /var/hls_live
RUN chown -R melted:melted /var/hls_live

RUN apt-get install -y cron telnet

COPY cron/hls /etc/cron.hourly

RUN service cron start



USER melted

COPY melted.conf /etc/melted/melted.conf
COPY melted-hls-vaapi.conf /etc/melted/melted-hls-vaapi.conf
COPY melted-rtsp-vaapi.conf /etc/melted/melted-rtsp-vaapi.conf
COPY melted-rtmp-vaapi.conf /etc/melted/melted-rtmp-vaapi.conf
#COPY live.sdp live.sdp

VOLUME /var/hls_live
VOLUME ["/home/melted/videos"]

EXPOSE 5250

CMD ["-c", "/etc/melted/melted.conf","-nodetach"]

ENTRYPOINT ["melted"]
