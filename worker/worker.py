import mysql.connector
import nltk
from nltk.sentiment import SentimentIntensityAnalyzer
from openai import OpenAI
import os
from dotenv import load_dotenv

load_dotenv()

OPENAI_API_KEY = os.getenv("OPENAI_API_KEY") 

try:
    nltk.data.find("sentiment/vader_lexicon.zip")
except LookupError:
    nltk.download("vader_lexicon")

sia = SentimentIntensityAnalyzer()
client = OpenAI(api_key=OPENAI_API_KEY)


# DB connection
db = mysql.connector.connect(
    host=os.getenv("UNGIZWE_DB_HOST", "ungizwe_db"),
    user=os.getenv("UNGIZWE_DB_USER", "ungizwe"),
    password=os.getenv("UNGIZWE_DB_PASS", ""),
    database=os.getenv("UNGIZWE_DB_NAME", "ungizwe"),
)

cursor = db.cursor(dictionary=True)


# After DB connection, add a migration-safe check:
cursor.execute("""
    ALTER TABLE cries ADD COLUMN IF NOT EXISTS processed TINYINT(1) DEFAULT 0
""")  # MySQL 8+; for older MySQL, run manually via migration instead

cursor.execute("SELECT id, brand, cry FROM cries WHERE processed = 0")
rows = cursor.fetchall()


def get_topic(text):
    response = client.chat.completions.create(
        model="gpt-4o-mini",
        messages=[
            {
                "role": "system",
                "content": "Extract a single short topic label (max 3 words) like: customer service, billing issue, workplace abuse, delivery delay."
            },
            {"role": "user", "content": text}
        ]
    )
    return response.choices[0].message.content.strip().lower()


def sentiment_score(text):
    return sia.polarity_scores(text)["compound"]


def upsert_score(brand, topic, score, sentiment):
    # check if exists
    cursor.execute("""
        SELECT id, num_supporting FROM brand_topic_scores
        WHERE brand=%s AND topic=%s
    """, (brand, topic))

    existing = cursor.fetchone()

    if existing:
        new_count = existing["num_supporting"] + 1

        cursor.execute("""
            UPDATE brand_topic_scores
            SET num_supporting=%s,
                score=(score + %s)/2
            WHERE id=%s
        """, (new_count, score, existing["id"]))

    else:
        cursor.execute("""
            INSERT INTO brand_topic_scores
            (brand, topic, num_supporting, score)
            VALUES (%s, %s, %s, %s)
        """, (brand, topic, 1, score))


for row in rows:
    brand = row["brand"]
    cry = row["cry"]

    topic = get_topic(cry)
    sentiment = sentiment_score(cry)

    # convert sentiment → your “cry score”
    cry_score = (1 - sentiment) * 50

    upsert_score(brand, topic, cry_score, sentiment)
    cursor.execute("UPDATE cries SET processed = 1 WHERE id = %s", (row["id"],))

    print(f"Processed: {brand} | {topic} | {cry_score}")


db.commit()
cursor.close()
db.close()
