##Example of Rest api to work with deferred calculations tasks
###Endpoints
- /api/v1/command/info?id=taskid - get information about task status
- /api/v1/command/create - create task. Request body: {"type":"task type","args":""}
- /api/v1/command/delete?id=taskid - delete task by identificator

All Endpoints require Authorization header

###Worker
- Worker start: ./worker.php
- Worker soft kill: kill -15 pid (worker ended current task end successfully stopped)

###Tests
- Test run: ./vendor/bin/codecept run --steps (unit and functional)