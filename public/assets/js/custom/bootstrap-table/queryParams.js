function queryParams(p) {
    return {
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        limit: p.limit,
        search: p.search,
    };
}

function reportReasonQueryParams(p) {
    return {
        "status": $('#filter_status').val(),
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        limit: p.limit,
        search: p.search
    };
}

function userListQueryParams(p) {
    return {
        "status": $('#filter_status').val(),
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        limit: p.limit,
        search: p.search
    };
}
