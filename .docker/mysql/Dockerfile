FROM mysql:latest

#RUN apt-get update && \
#    apt-get install -y mysql-server && \
#    rm -rf /var/lib/apt/lists/*

#RUN apt-get update && apt-get install -y gettext && rm -rf /var/lib/apt/lists/*

# Définir les variables d'environnement
ENV MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
ENV MYSQL_DATABASE=${MYSQL_DATABASE}
ENV MYSQL_USER=${MYSQL_USER}
ENV MYSQL_PASSWORD=${MYSQL_PASSWORD}
ENV MYSQL_SERVER=${MYSQL_SERVER}
ENV MYSQL_PORT=${MYSQL_PORT}

COPY ./.docker/mysql/init.sql /docker-entrypoint-initdb.d/

#EXPOSE ${MYSQL_PORT}

#CMD ["/bin/bash", "-c", "/docker-entrypoint-initdb.d/init.sh && exec mysqld"]
CMD ["mysqld"]
