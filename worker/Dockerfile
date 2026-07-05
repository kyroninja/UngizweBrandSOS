FROM python:3.11-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
        cron \
        default-libmysqlclient-dev \
        build-essential \
        pkg-config \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY worker.py .
COPY requirements.txt .

RUN pip install --no-cache-dir -r requirements.txt

# Bake the NLTK lexicon into the image at build time so cron runs
# never need internet access just to check for sentiment data.
RUN python -c "import nltk; nltk.download('vader_lexicon')"

# Cron needs env vars explicitly exported into a file cron will source,
# since cron jobs don't inherit the container's environment otherwise.
COPY worker-cron /etc/cron.d/worker-cron
RUN chmod 0644 /etc/cron.d/worker-cron && crontab /etc/cron.d/worker-cron

RUN touch /var/log/worker.log

# docker-compose passes env vars into the container's own environment,
# but cron jobs run in a fresh shell that does NOT inherit them. So on
# startup we dump the container's env into a file the cron job sources
# explicitly before running python (see worker-cron).
CMD printenv | grep -E '^(UNGIZWE_|OPENAI_)' | sed 's/^/export /' > /app/env.sh && \
    cron && \
    tail -f /var/log/worker.log
