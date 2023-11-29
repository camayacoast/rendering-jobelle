import Http from 'utils/Http'
import { useQuery, useMutation } from 'react-query'

const create = () => {

        return useMutation(async formData => {
            return await Http.post('/api/booking/meal/create', formData)
        });

}

const list = () => {

    return useQuery("meals", async () => {
        const { data } = await Http.get(`/api/booking/meals`);
        return data;
    },{
        refetchOnWindowFocus: false,
    });

}

const guesetMealList = () => {

    return useQuery("guest_meals", async () => {
        const { data } = await Http.get(`/api/booking/guest/meals`);
        return data;
    },{
        refetchOnWindowFocus: false,
    });

}

const scan = () => {
    return useMutation(async formData => {
        return await Http.put('/api/booking/guest/meals/scan', formData)
    });
  };

const remove = () => {
    return useMutation(async (mealId) => {
      // Send a DELETE request to delete the meal with the given ID
      await Http.put(`/api/booking/meal/delete/${mealId}`);
    });
  };

export default {
    create,
    list,
    scan,
    guesetMealList,
    remove
}
