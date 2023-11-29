import React,{useState} from 'react'
import moment from 'moment-timezone'
moment.tz.setDefault('Asia/Manila');
import MealService from 'services/Booking/MealService'
import { Table, Button, Typography, message, Row, Col, Form, Modal, Input, Select , DatePicker, Popconfirm, Tag } from 'antd'
const { TextArea } = Input;
import Icon, { EditOutlined, LinkOutlined, DeleteOutlined } from '@ant-design/icons'
import {queryCache} from 'react-query'




function Page(props) {

    const mealListQuery = MealService.list();

    const [newMealQuery, {isLoading: newMealQueryIsLoading}] = MealService.create();
    const [mealModalVisible, setMealModalVisible] = React.useState(false);
    const [newMealForm] = Form.useForm();
    const [removeMeal] = MealService.remove();

    const [newMealScanQuery, {isLoading: newMealScanQueryIsLoading}] = MealService.scan();
    const mealScanListQuery = MealService.guesetMealList();
    const [mealScanModalVisible, setMealScanModalVisible] = React.useState(false);
    const [newMealScanForm] = Form.useForm();
    const [form] = Form.useForm();

    const [isSaving, setIsSaving] = useState(false);


    const NewMealForm = (props) => (
        <Form {...props}>
            <Row gutter={[8,8]}>
                <Col xl={24}>
                    <Form.Item label="Meal" name="Meal" rules={[{required: true}]}>
                        <Input />
                    </Form.Item>
                </Col>

                <Col xl={24}>
                    <Form.Item label="Type" name="type">
                        <Select>
                            <Select.Option value="Breakfast">Breakfast</Select.Option>
                            <Select.Option value="Lunch">Lunch</Select.Option>
                            <Select.Option value="Dinner">Dinner</Select.Option>
                        </Select>
                    </Form.Item>
                </Col>

                <Col xl={12}>
                    <Form.Item label="From" name="available_from">
                        <DatePicker />
                    </Form.Item>
                </Col>

                <Col xl={12}>
                    <Form.Item label="To" name="available_to">
                        <DatePicker />
                    </Form.Item>
                </Col>

                <Col xl={24}>
                    <Form.Item label="Description" name="description" className="custom-textarea">
                        <TextArea rows={6} placeholder="Description" maxLength={155} />
                    </Form.Item>
                </Col>

                <Col xs={24} align="right">
                    <Button  htmlType="submit" disabled={isSaving}>Save</Button>
                </Col>

            </Row>
        </Form>
    )

    const NewMealScanForm = (props) => (


        <Form {...props} form={form}>
            <Row gutter={[8,8]}>
                <Col xl={24}>
                    <Form.Item label="Guest Ref" name="booking_ref" rules={[{required: true}]}>
                        <Input />
                    </Form.Item>
                </Col>
                <Col xl={24}>
                    <Form.Item label="Meal to Claim" name="type" rules={[{required: true}]}>
                        <Select>
                            <Select.Option value="Breakfast">Breakfast</Select.Option>
                            <Select.Option value="Lunch">Lunch</Select.Option>
                            <Select.Option value="Dinner">Dinner</Select.Option>
                        </Select>
                    </Form.Item>
                </Col>

                <Col xs={24} align="right">
                <Popconfirm
              title="Are you sure you want to claim this meal?"
              onConfirm={() => {
                form.submit();
              }}
              okText="Yes"
              cancelText="No"
            >
              <Button htmlType="submit">Claim</Button>
            </Popconfirm>
                </Col>

            </Row>
        </Form>
    )

    const newMealFormOnFinish = (values) => {
        setIsSaving(true);

        newMealQuery(values, {
            onSuccess: (res) => {
                console.log(res);
                setMealModalVisible(false);
                newMealForm.resetFields();
                message.success('Added Meal successfully!');

                queryCache.setQueryData("meals", prev => [...prev, res.data]);
            },
            onError: (e) => {
                console.log(e);
                message.info(e.message);
            },
            // Set isSaving back to false when the save operation is complete
            onSettled: () => {
                setIsSaving(false);
            },
        });
    }

    const newMealScanFormOnFinish = (values) => {
          newMealScanQuery(values, {
            onSuccess: (res) => {
              console.log(res);
              setMealScanModalVisible(false);
              form.resetFields();
              message.success('Meal Claimed successfully!');

              queryCache.invalidateQueries('guest_meals');
            },
            onError: (e) => {
              console.log(e);
              message.info(e.message);
            },
          });

      };

    const handleDelete = (mealId) => {
    // Use the remove mutation to delete the meal
    removeMeal(mealId, {
      onSuccess: () => {
        // Remove the deleted meal from the UI by updating the data source
        queryCache.setQueryData(
          'meals',
          (prev) => prev.filter((meal) => meal.id !== mealId)
        );

        message.success('Meal deleted successfully!');
      },
      onError: (error) => {
        console.error('Error deleting meal:', error);
        message.error('An error occurred while deleting the meal.');
      },
    });
  };


    return (
        <>
            <Typography.Title level={5}>Meals</Typography.Title>
            <Button onClick={()=>setMealModalVisible(true)}>New Meal</Button>
            <Modal
                title="New Meal"
                visible={mealModalVisible}
                onCancel={()=>setMealModalVisible(false)}
                footer={null}
            >
                <NewMealForm
                    form={newMealForm}
                    layout="vertical"
                    onFinish={newMealFormOnFinish}
                    initialValues={{
                        starttime: moment('00:00:00', 'HH:mm:ss'),
                        endtime: moment('23:59:00', 'HH:mm:ss'),
                    }}
                />
            </Modal>
            <Table
                size="small"
                dataSource={mealListQuery.data}
                loading={mealListQuery.isLoading}
                rowKey="id"
                columns={[
                    {
                        title: 'Meal No.',
                        dataIndex: 'id',
                        key: 'id',
                    },
                    {
                        title: 'Name',
                        dataIndex: 'name',
                        key: 'name',
                    },
                    {
                        title: 'Type',
                        dataIndex: 'type',
                        key: 'type',
                    },
                    {
                        title: 'Available From',
                        dataIndex: 'available_from',
                        key: 'available_from',
                    },
                    {
                        title: 'Available To',
                        dataIndex: 'available_to',
                        key: 'available_to',
                    },
                    {
                        title: 'Description',
                        dataIndex: 'description',
                        key: 'description',
                    },
                    {
                        title: 'Action', // New "Action" column
                        key: 'action',
                        render: (text, record) => (
                            <Popconfirm
                                title="Are you sure you want to delete this meal?"
                                onConfirm={() => handleDelete(record.id)} // Call handleDelete with meal ID
                                okText="Yes"
                                cancelText="No"
                            >
                                <a href="#">
                                <Button danger icon={<DeleteOutlined />} />

                                </a>
                            </Popconfirm>
                        ),
                    },
                ]}
            />

            <Typography.Title level={5}>DDT Meals</Typography.Title>
            <Button onClick={()=>setMealScanModalVisible(true)}>Scan Guests Booking</Button>
            <Modal
                title="Scan Booking"
                visible={mealScanModalVisible}
                onCancel={()=>setMealScanModalVisible(false)}
                footer={null}
            >
                 <NewMealScanForm
                    form={newMealScanForm}
                    layout="vertical"
                    onFinish={newMealScanFormOnFinish}
                />
            </Modal>
            <Table
                size="small"
                dataSource={mealScanListQuery.data}
                loading={mealScanListQuery.isLoading}
                rowKey="guest_meal_id"
                columns={[
                    {
                        title: '#',
                        dataIndex: 'guest_meal_id',
                        key: 'guest_meal_id',
                    },
                    {
                        title: 'Booking Ref#',
                        dataIndex: 'booking_reference',
                        key: 'booking_reference',
                    },
                    {
                        title: 'Guest Ref#',
                        dataIndex: 'guest_reference_number',
                        key: 'guest_reference_number',
                    },
                    {
                        title: 'Guest Name',
                        dataIndex: 'guests_name',
                        key: 'guests_name',
                    },
                    {
                        title: 'Guest Age',
                        dataIndex: 'age',
                        key: 'age',
                    },
                    {
                        title: 'Meal',
                        dataIndex: 'meal_name',
                        key: 'meal_name',
                    },
                    {
                        title: 'Meal Type',
                        dataIndex: 'meal_type',
                        key: 'meal_type',
                    },
                    {
                        title: 'Remaining',
                        dataIndex: 'remaining',
                        key: 'remaining',
                    },
                    {
                        title: 'Booking Type',
                        dataIndex: 'booking_type',
                        key: 'booking_type',
                    },
                    {
                        title: 'Status',
                        dataIndex: 'status',
                        key: 'status',
                        render: (status) => {
                            if (status === 'Claimed') {
                              return <Tag color="green">{status}</Tag>;
                            } else {
                              return <Tag color="warning">{status}</Tag>;
                            }
                          },
                    },
                ]}
            />
        </>
    )
}

export default Page;
