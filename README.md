# Scrolly Moodle Filter

Scrolly is a lightweight Moodle filter plugin that allows you to embed interactive content directly inside course text.

It supports simple interactive components such as:
- True/False questions
- Multiple choice questions (single answer)
- Accordion (expandable content blocks)

The goal of Scrolly is to provide **H5P-like interactivity** with a **minimal and simple syntax**, without requiring complex setup.

## Requirements
- Moodle 4.1 or higher

## Installation
Upload the ZIP file via Moodle plugin installation and follow the steps.  
Don't forget to enable the filter after installation.

## Usage
You can use Scrolly anywhere Moodle filters are supported (e.g. labels, pages, questions).

## Features
- True/False questions
- Multiple choice (single answer)
- Accordion content blocks

## Examples

### True / False

```text
[scrolly]
type: "truefalse"
question: "The Earth is flat"
correct: "false"
correctfeedback: "Correct! The Earth is round."
incorrectfeedback: "Not correct. The Earth is not flat."
truelabel: "True"

[scrolly]
type: "multichoice"
question: "What is 2 + 2?"
options: "1|2|3|4"
correct: "4"
correctfeedback: "Correct!"
incorrectfeedback: "That is not correct."
checklabel: "Check answer"
resetlabel: "Try again"
[/scrolly]

[scrolly]
type: "accordion"
title: "My accordion"
items: "Item 1::Content 1||Item 2::Content 2||Item 3::Content 3"
[/scrolly]
