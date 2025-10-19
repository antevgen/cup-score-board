# Football World Cup Score Board (PHP In-Memory Implementation)

## Overview

This project implements a simple in-memory Football World Cup Score Board, based on the following requirements:

- Start a game (initial score `0 - 0`)
- Update a game’s score
- Finish a game (remove it from the board)
- Get a summary of games ordered by total score (descending), breaking ties by most recently started match first

## Features

- Start a new match with StartFootballGameEvent
- Prevent starting the same match twice
- Prevent a team from playing in multiple matches simultaneously
- Update match scores
- Finish a match and free teams to play again
- Summary of matches sorted by:
  - Total score (highest first)
  - Most recently started (newest first if tied)

## Tests

The project includes PHPUnit tests verifying:

- A match can be started only once
- Teams cannot appear in more than one match at the same time
- Removing a match frees teams to play again
- Summary is ordered by total score, then by recency

Run tests with:

``` bash
vendor/bin/phpunit --testdox
```
