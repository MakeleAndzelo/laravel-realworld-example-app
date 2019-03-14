Feature: A user can log in to an account
  Scenario: Login to an account
    When I add "Content-type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users/login" with body:
    """
    {
      "user": {
        "email": "example@example.org",
        "password": "secret1234"
      }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be valid according to this schema:
    """
    {
      "properties": {
        "user": {
          "type": "object",
          "required": true,
          "token": {
            "type": "string"
          }
        }
      }
    }
    """
