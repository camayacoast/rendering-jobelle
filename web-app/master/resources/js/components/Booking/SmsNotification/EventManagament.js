import React, { useEffect, useState } from 'react'
import moment from 'moment-timezone'
import Loading from 'common/Loading'

import SmsNotificationServices from 'services/Booking/SmsNotificationServices'

import { Row, Col, Card, Typography, Space, Button, Input, Form, Drawer, message, Upload, Table} from 'antd'
import { PlusOutlined, MinusCircleOutlined, UploadOutlined, DownloadOutlined} from '@ant-design/icons'

import SmsEvent from './SmsEvent'
import EventJobHistory from './EventJobHistory'

function EventManagament(props) {

    const {data: smsEvent, isLoading: smsEventLoading, refetch: smsEventRefetch} = SmsNotificationServices.getSmsEvent();

    const {data: eventJobHistoryData, isLoading: eventJobHistoryLoading, refetch: eventJobRefetch} = SmsNotificationServices.getSmsEventJobHistory();

    const [createEvent, { isLoading: createEventLoading}] = SmsNotificationServices.createSmsEvent();

    const [removeJob, { isLoading: removeJobLoading}] = SmsNotificationServices.removeJobs();

    const [eventForm] = Form.useForm();

    const [eventFormUpload] = Form.useForm();

    const [openEventForm, setOpenEventForm] = useState(false);

    const { TextArea } = Input;

    const [fileList, setFileList] = useState([]);

    const [uploading, setUploading] = useState(false);
  
    const [importedData, setImportData] = useState([]);

    const [openImportBulkDrawer, setOpenImportBulkDrawer] = useState(false);

    const uploadProps = {
        onRemove: file => {
           setFileList( prev => {
             const index = prev.indexOf(file);
             const newFileList = prev.slice();
             newFileList.splice(index, 1);
    
             return newFileList;
           });
         },
         method: 'post',
         name: 'file',
         action: `${process.env.APP_URL}/api/sms-notification/import-recepients`,
         headers:{
             Authorization: `Bearer ${localStorage.getItem('token')}`,
             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
         },
         beforeUpload: (file) => {
    
           const excelFileType = [
               'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
               // 'application/vnd.ms-excel'
           ];
    
           const isExcel = excelFileType.includes(file.type);
    
           if (!isExcel) {
             message.error(`${file.name} is not a .xlsx file`);
           }
    
           setUploading(true);
           
           return isExcel || Upload.LIST_IGNORE;
         },
         onSuccess: (res) => {
            
            message.success('Successfully Uploaded!');
            setUploading(false);
            setOpenImportBulkDrawer(true);
            setImportData(res.data);

            console.log(res.data)
         },
         onChange(info) {
           console.log(info)
         },
       fileList
     }

    const onCreateEvent = values => {

        if(createEventLoading) return false;

        createEvent({...values}, {
            onSuccess: (res) => {
                message.success('Event successfully created!');
                smsEventRefetch();
                eventForm.resetFields();
            },
            onError: (e) => {
                message.error(e.message);
            }
        });
    }

    const createEventWithUpload = values => {

        if(createEventLoading) return false;

        createEvent({...values, recepients: importedData}, {
            onSuccess: (res) => {
                message.success('Event successfully created!');
                smsEventRefetch();
                eventFormUpload.resetFields();
            },
            onError: (e) => {
                message.error(e.message);
            }
        });
    }

    const onRemoveJobs = id => {

        if(removeJobLoading) return false;

        removeJob({id: id}, {
            onSuccess: (res) => {
                message.success('Event successfully cancelled!');
                eventJobRefetch();
            },
            onError: (e) => {
                message.error(e.message);
            }
        });
    }

    const handleOpenEventDrawer = () => {
        setOpenEventForm(true);
    }

    const handleClosEventDrawer = () => {
        setOpenEventForm(false);
    }

    return (
        <>
           <Typography.Title level={3}>Event Management</Typography.Title>

           <Space>
              <Button type="danger" onClick={handleOpenEventDrawer}> <PlusOutlined/> Add Event</Button>

              <Upload {...uploadProps}>
                <Button type="danger" loading={uploading}> <UploadOutlined/> Import Recipients</Button>
            </Upload>

            <Button type="danger" onClick={() => SmsNotificationServices.downloadRecepientTemplate({})}> <DownloadOutlined/> Download Recipient Template</Button>
           </Space>

           <Card title={<>List Of Events </>} size="medium" bordered={true} className="card-shadow mt-3">
              <SmsEvent data={smsEvent ?? []} loading={smsEventLoading} refetch={() => smsEventRefetch()}/>
           </Card>

           <Card title={<>Events Job History </>} size="medium" bordered={true} className="card-shadow mt-3">
              <EventJobHistory data={eventJobHistoryData ?? []} handleRemove={onRemoveJobs} loading={eventJobHistoryLoading} exportData={SmsNotificationServices.downloadEventData}/>
           </Card>

           <Drawer
                title="Event Form"
                placement="right"
                closable={true}
                onClose={handleClosEventDrawer}
                visible={openEventForm}
                width={620}
            >

            <Form layout="vertical" form={eventForm} autoComplete="off" onFinish={onCreateEvent}>
                
                <Row gutter={[5,0]}>
                    <Col md={24}>
                        <Form.List name="recepients" initialValue={[{recepient: ''}]}>
                            {(fields, { add, remove }) => (
                                <>
                                    {fields.map(({ key, name, ...restField }) => (
                                        <Space
                                            key={key}
                                            style={{
                                                display: 'flex',
                                                marginBottom: 8,
                                            }}
                                            align="baseline"
                                        >
                                        <Form.Item
                                                {...restField}
                                                name={[name, 'recepient']}
                                            
                                                rules={[
                                                    {
                                                        required: true,
                                                        message: 'Recipient # is required',
                                                    },
                                                    {
                                                        pattern: /^(09|\+639)\d{9}$/,
                                                        message: 'Please enter a valid Philippine mobile number',
                                                    },
                                                ]}
                                            >
                                                <Input style={{width: '250px'}} placeholder="Recipient #" />
                                        </Form.Item>

                                        <Form.Item
                                                {...restField}
                                                name={[name, 'recepient_name']}
                                            
                                                rules={[
                                                    {
                                                        required: true,
                                                        message: 'Recipient name is required',
                                                    },
                                                ]}
                                            >
                                                <Input style={{width: '270px'}} placeholder="Recipient name" />
                                        </Form.Item>

                                          {
                                            fields.length > 1 ?
                                            <MinusCircleOutlined onClick={() => remove(name)} />  :
                                            null
                                          }

                                        </Space>
                                    ))}
                                    <Form.Item>
                                        <Button type="dashed" onClick={() => add()} block icon={<PlusOutlined />}>
                                           Add Recipient
                                        </Button>
                                    </Form.Item>
                                </>
                            )}
                        </Form.List>
                    </Col>

                    <Col md={24}>
                        <Form.Item name="event_name" label="Event Name" rules={[{ required: true, message: 'Event name is required' }]}>
                            <Input size="large"/>
                        </Form.Item>
                    </Col>

                    <Col md={24}>
                        <Form.Item name="event_description" label="Event Description">
                            <TextArea size="large"/>
                        </Form.Item>
                    </Col>

                    <Col md={24}>
                        <Form.Item name="message" label="Message" rules={[{ required: true, message: 'message is required' }]}>
                            <TextArea size="large"/>
                        </Form.Item>
                    </Col>

                </Row>
                
                <Button type="danger" style={{float: "right"}} loading={createEventLoading} size="middle" htmlType="submit">Add Event</Button>
                
            </Form>

        </Drawer>


        <Drawer
                title="Add Event"
                placement="right"
                closable={true}
                onClose={() => setOpenImportBulkDrawer(false)}
                visible={openImportBulkDrawer}
                width={720}
            >
                
                  <Table
                        title={() => <>Confirm Excel Upload</>}
                        rowKey="id"
                        dataSource={importedData ?? []}
                        size="small"
                        columns={[
                            {
                                title: "Recipient #",
                                render: (text, record) => {
                                    return record.recepient;
                                }
                            },
                            {
                                title: "Recipient Name",
                                render: (text, record) => {
                                    return record.recepient_name;
                                }
                            },

                        ]}
                    />
                

            <Form layout="vertical" autoComplete="off" form={eventFormUpload} onFinish={createEventWithUpload}>
                
                <Row gutter={[5,0]}>

                    
                    <Col md={24}>
                            <Form.Item name="event_name" label="Event Name" rules={[{ required: true, message: 'Event name is required' }]}>
                                <Input size="large"/>
                            </Form.Item>
                        </Col>

                        <Col md={24}>
                            <Form.Item name="event_description" label="Event Description">
                                <TextArea size="large"/>
                            </Form.Item>
                        </Col>

                        <Col md={24}>
                            <Form.Item name="message" label="Message" rules={[{ required: true, message: 'message is required' }]}>
                                <TextArea size="large"/>
                            </Form.Item>
                        </Col>

                </Row>
                
                <Button type="danger" style={{float: "right"}} loading={createEventLoading} size="middle" htmlType="submit"><PlusOutlined/> Add Event</Button>
                
            </Form>

        </Drawer>
        </>
    );
}

export default EventManagament;