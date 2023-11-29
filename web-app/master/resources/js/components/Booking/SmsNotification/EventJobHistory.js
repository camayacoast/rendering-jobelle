import React, { useEffect, useState } from 'react'
import { Button, Space, Table } from 'antd'
import moment from 'moment';

function EventJobHistory(props) {
    const { data, loading, handleRemove, exportData } = props;

    const columns = [
        {
            title: "#",
            width: 50,
            render: (text, record) => {
                return record.id;
            }
        },
        {
            title: "Event Name",
            render: (text, record) => {
                return record.event_name;
            }
        },
        {
            title: "Event Description",
            width: 200,
            ellipsis: true,
            render: (text, record) => {
                return record.event_description;
            }
        },
        {
            title: "Sending Type",
            render: (text, record) => {
                return record.sending_type;
            }
        },
        {
            title: "Sending Date",
           
            render: (text, record) => {
                return moment(record.sending_date).format('LLL')
            }
        },
        {
            title: "Status",
            render: (text, record) => {
                const status = record.status;

                if(status === 'Success') {

                    return <strong style={{color: "green"}}>{ status }</strong>;

                } else if(status === 'Pending') {

                    return <strong style={{color: "orange"}}>{ status }</strong>;
                }

                return <strong style={{color: "red"}}>{ status }</strong>;
            }
        },
        {
            title: "Created At",
            render: (text, record) => {
                return record.created_at;
            }
        },
        {
            title: "Action",
            align: "center",
            render: (text, record) => {
                return <Space>
                    <Button size="small" onClick={() => {

                        if(confirm('Are you sure you want to cancel this event?') === false) return false;

                          handleRemove(record.job_id);

                        }} disabled={record.status === 'Pending' ? false : true} danger>Cancel</Button>

                     <Button size="small" danger onClick={() => exportData({id: record.id})}>Export</Button>
                </Space>;
            }
        },
    ];
  return (
     <>
         <Table
            size="small"
            rowKey="id"
            dataSource={data}
            loading={loading}
            columns={columns}
        />
     </>
  );
}

export default EventJobHistory;