# pull official base image
FROM python:3.9.6-slim

# set work directory
WORKDIR /usr/src/performance

# set environment variables
ENV PYTHONDONTWRITEBYTECODE 1
ENV PYTHONUNBUFFERED 1

# install psycopg2 dependencies
RUN apt-get update \
    && apt-get install -y gcc python3-dev musl-dev libpq-dev


# install dependencies
RUN pip install --upgrade pip
COPY ./requirements.txt .
RUN pip install -r requirements.txt

# fetch models from database
# RUN python manage.py inspectdb > models.py
# RUN python manage.py migrate

# copy project
COPY . .
