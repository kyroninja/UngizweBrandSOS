# BrandSOS

Anonymous brand intelligence platform that transforms customer and employee experiences into structured sentiment signals and topic-based pain point scores.

## Overview

BrandSOS captures anonymous "cries for help" from customers and employees, then converts them into actionable brand intelligence using AI-powered topic extraction and sentiment analysis.

The goal is simple: help organizations identify emerging issues before they become crises.

## Current Features

### Frontend

* Anonymous cry submission form
* Brand selection and experience capture
* Responsive web interface

### Backend

* PHP form processing
* MySQL data storage
* Secure environment variable management
* Python processing pipeline

### AI & Analytics

* OpenAI-powered topic extraction
* VADER sentiment analysis
* Brand-topic aggregation
* Topic-level pain point scoring

## Architecture

Frontend → PHP → MySQL (`cries`)
↓
Python Intelligence Layer
↓
OpenAI Topic Classification
↓
VADER Sentiment Analysis
↓
`brand_topic_scores`
↓
Brand Insights

## Database Design

### cries

Stores raw anonymous submissions.

| Column    | Description     |
| --------- | --------------- |
| id        | Primary key     |
| timestamp | Submission time |
| brand     | Brand name      |
| cry       | Raw submission  |

### brand_topic_scores

Stores aggregated intelligence.

| Column         | Description                |
| -------------- | -------------------------- |
| brand          | Brand name                 |
| topic          | Extracted topic            |
| num_supporting | Number of supporting cries |
| score          | Topic pain score           |

## Build Log

### Part 1

* Website prototype
* Cry submission workflow
* Database design
* Brand intelligence concept

### Part 2

* MySQL integration
* Python environment setup
* OpenAI integration
* Topic extraction pipeline
* Sentiment analysis foundation
* Brand-topic scoring architecture

## Roadmap

* [ ] Real-time processing
* [ ] Topic clustering
* [ ] Brand heat maps
* [ ] Trend detection
* [ ] Employee vs Customer analytics
* [ ] Early warning alerts
* [ ] Public transparency dashboard

## Tech Stack

* PHP
* MySQL
* Python
* OpenAI API
* NLTK VADER
* HTML/CSS/JavaScript
* XAMPP
