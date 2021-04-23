create table auth
(
    id INTEGER PRIMARY KEY,
    data_source   text not null,
    client_id text not null,
    access_token text not null,
    access_token_name text not null,
    expiry INT not null
);

create table states
(
    id INTEGER PRIMARY KEY,
    data_source   text not null,
    data_type text not null,
    counter text not null,
    counter_state INT not null,
    request_id text not null
);

create table posts
(
    id INTEGER PRIMARY KEY,
    data_source  text not null,
    post_id   text not null,
    user_name text not null,
    user_id text not null,
    message text not null,
    created text not null,
    type text not null,
    meta_chars INT not null,
    meta_day INT not null,
    meta_week INT not null,
    meta_month INT not null,
    meta_year INT not null
);
