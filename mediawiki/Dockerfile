FROM debian:sid
MAINTAINER Boris Fritscher
ENV MEDIAWIKI_VERSION REL1_27

RUN set -x; \
    apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        apache2 \
        libapache2-mod-php \
        php-mysql \
        php-cli \
        php-gd \
        php-curl \
        php-pear \
        php-mbstring \
        php-mail \
        php-net-smtp \
        imagemagick \
        netcat \
        curl \
        git \
        zip \
        unzip \
        bzip2 \
        ghostscript \
        xpdf-utils \
        python-pygments \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /var/cache/apt/archives/* \
    && a2enmod rewrite \
    && a2enmod proxy \
    && a2enmod proxy_http \
    && rm -r /var/www/html

# Install MediaWiki
RUN cd /var/www \
&& curl -O https://releases.wikimedia.org/mediawiki/1.27/mediawiki-1.27.1.tar.gz \
&& tar xvzf mediawiki-*.tar.gz \
&& rm mediawiki-*.tar.gz \
&& mv mediawiki-* html

# Install VisualEditor
RUN cd /var/www/html/extensions \
&& git clone -b $MEDIAWIKI_VERSION https://gerrit.wikimedia.org/r/p/mediawiki/extensions/VisualEditor.git \
&& cd VisualEditor \
&& git submodule update --init

# Install MediaWiki_Language_Extension_Bundle
RUN cd /var/www/html \
&& curl -O https://translatewiki.net/mleb/MediaWikiLanguageExtensionBundle-2016.08.tar.bz2 \
&& tar xjf MediaWikiLanguageExtensionBundle-*.tar.bz2 \
&& rm MediaWikiLanguageExtensionBundle-*.tar.bz2


# Configure
COPY php.ini /etc/php/7.0/apache2/conf.d/mediawiki.ini
COPY apache/mediawiki.conf /etc/apache2/
RUN echo "Include /etc/apache2/mediawiki.conf" >> /etc/apache2/apache2.conf
COPY conf /conf_default
RUN mkdir /conf
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80 443
VOLUME ["/var/www/html/images", "/conf"]
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apachectl", "-e", "info", "-D", "FOREGROUND"]