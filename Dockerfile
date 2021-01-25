FROM phpdockerio/php72-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
&& apt-get -y --no-install-recommends install  php7.2-mysql php-redis php-xdebug php7.2-bcmath php7.2-bz2 php7.2-gd php7.2-intl php-ssh2 php7.2-xsl php-yaml \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install phalcon
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash \
&& apt-get install -y php7.2-phalcon=3.4.2-2+php7.2 \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update \
&& apt-get -y install git \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Phalcon dev tools
RUN cd ~  && git clone https://github.com/cembeliq/phalcon-devtools-3.4.git phalcon-devtools \
&& cd phalcon-devtools/ && ./phalcon.sh \
&& rm -rf /usr/bin/phalcon  \
&& ln -s ~/phalcon-devtools/phalcon /usr/bin/phalcon  \
&& chmod ugo+x /usr/bin/phalcon \
&& composer install


