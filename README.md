# Web Platform for Online Survey System / Anketarko

## Project Description
The web platform for implementing an online survey system allows users to create surveys, ask questions, and track responses from other users in real-time. The platform provides an intuitive interface for creating various surveys, as well as the ability to visualize and analyze the collected data. Users can participate in surveys with predefined responses such as "Agree" and "Disagree," while administrators have additional privileges such as creating survey categories and monitoring user activity.

## Key Features
- **Survey Creation**: Users can create surveys, add questions, and select types of responses.
- **Participation in Surveys**: Users can participate in public and private surveys.
- **Data Analysis**: Survey results can be analyzed through graphical representations and statistical tracking.
- **Administration**: Administrators can manage categories, monitor users, and oversee the status of surveys.
- **Security**: The system ensures the protection of user data and survey results, including verification using Tesseract and CAPTCHA during registration.

## Technologies
- **Backend**: PHP
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL
- **Security**: Tesseract (for verification) and CAPTCHA to protect against bots

## Entities in the System
1. **User**: Contains information about users such as first name, last name, email address, administrator status, registration date, and user image for verification.
2. **Survey**: Stores data about surveys created by users. Each survey has a title, description, visibility status, and a link to its category.
3. **Question**: Each question is part of a survey and has text and a unique identifier.
4. **Response**: Users can provide answers to questions, which are stored with a link to the question and user.
5. **Category**: Categories organize surveys by topics.
6. **Frequently Asked Questions (FAQ)**: Provides information on common questions regarding the use of the platform.

## Screenshots
## Capture 1
![Capture1](./Screenshots/Capture.jpg)

## Capture 2
![Capture2](./Screenshots/Capture1.jpg)

## Capture 3
![Capture3](./Screenshots/Capture2.jpg)

## Capture 4
![Capture3](./Screenshots/Capture3.jpg)