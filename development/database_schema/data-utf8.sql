use phpscheduleit2;

insert into user_statuses values (1, 'Active'), (2, 'Awaiting'), (3, 'Inactive');
insert into roles values (1, 'User', 1), (2, 'Admin', 2);
insert into reservation_types values (1, 'Reservation'), (2, 'Blackout');
insert into reservation_statuses values (1, 'Created'), (2, 'Deleted'), (3, 'Pending');
insert into resource_types values (1, 'Resource'), (2, 'Accessory');