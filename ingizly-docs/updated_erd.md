```mermaid
erDiagram
    USERS ||--|| ADMINS : is_a
    USERS ||--|| SERVICE_BUYERS : is_a
    USERS ||--|| SERVICE_PROVIDERS : is_a
    USERS ||--o{ REVIEWS : writes
    USERS ||--o{ REPORTS : reports
    USERS ||--o{ NOTIFICATIONS : receives
    SERVICE_BUYERS ||--o{ ORDERS : places
    SERVICES ||--o{ ORDERS : has
    SERVICES ||--o{ REVIEWS : has
    SERVICES ||--o{ REPORTS : has
    SERVICES ||--|| CATEGORIES : belongs_to
    SERVICE_PROVIDERS ||--o{ SERVICES : offers
    SERVICE_PROVIDERS ||--o{ AVAILABILITY : sets
    ORDERS ||--o{ PAYMENTS : has
    CATEGORIES ||--o{ CATEGORIES : has_subcategory

    USERS {
        int id PK
        string name
        string email
        string password
        enum role
        boolean is_email_verified
        string profile_image
        datetime created_at
        datetime last_login
    }

    ADMINS {
        int user_id PK
    }

    SERVICE_BUYERS {
        int user_id PK
        string location
        string phone
    }

    SERVICE_PROVIDERS {
        int user_id PK
        string phone
        string bio
        string location
        string business_name
        string business_address
        float avg_rating
        enum provider_type
        boolean is_verified
    }

    CATEGORIES {
        int id PK
        string name
        string icon
        int parent_category_id FK
        datetime created_at
    }

    SERVICES {
        int id PK
        int category_id FK
        int provider_id FK
        string title
        text description
        decimal price
        int view_count
        enum status
        enum service_type
        text images
        string location
        datetime created_at
        datetime updated_at
    }

    AVAILABILITY {
        int id PK
        int provider_id FK
        enum day_of_week
        time start_time
        time end_time
        boolean is_available
    }

    ORDERS {
        int id PK
        int service_id FK
        int buyer_id FK
        enum status
        decimal total_amount
        date scheduled_date
        time scheduled_time
        string location
        text special_instructions
        datetime created_at
        datetime updated_at
    }

    PAYMENTS {
        int id PK
        int order_id FK
        decimal amount
        enum payment_method
        enum status
        string transaction_id
        datetime created_at
        datetime updated_at
    }

    REVIEWS {
        int id PK
        int service_id FK
        int user_id FK
        int order_id FK
        int rating
        text comment
        datetime created_at
    }

    REPORTS {
        int id PK
        int user_id FK
        int service_id FK
        text reason
        enum status
        text admin_notes
        datetime created_at
        datetime resolved_at
    }

    NOTIFICATIONS {
        int id PK
        int user_id FK
        string title
        text content
        boolean is_read
        enum notification_type
        datetime created_at
    }