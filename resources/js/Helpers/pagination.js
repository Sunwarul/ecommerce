import { router } from '@inertiajs/react';

export const handlePagination = (event, indexRoute, resource) => {
    router.get(indexRoute, {
        page: event.page + 1,
        per_page: event.rows
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['items']
    });
};
