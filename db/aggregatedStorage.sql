create table aggregated_posts
(
    id INTEGER PRIMARY KEY,
    data_source  text not null,
    aggregated_code text not null,
    aggregated_value REAL not null,
    scope text not null,
    day INT not null,
    week INT not null,
    month INT not null,
    year INT not null
);


/*
 in aggregated_posts replacing day,week, month, year can be replaced by date_from and date_to and scope will indicate the date range,
 ie. scope = day, scope_from = 2021-01-01 scope_to = 2021-01-02, or scope = seconds, scope_from=2021-01-01 12:00:00, scope_to=2021-01-01 12:00:01
 */