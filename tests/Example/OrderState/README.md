# Order state example

In order state example I would like to demonstrate that there are more then one solution of domain problem of order state which can transition into another states.

## 1. Class constants

[source code](refactoring-1.phpt)

There are public constants on class and you should figure out that you should put them into `canDoTransition()` method. There is nothing on type-level that helps you with that. Please note that all logic is in `OrderService`.

## 2. Dumb type-safe enum

[source code](refactoring-2.phpt)

This test shows usage of explicitly-declared dumb-enum.

I have explicitly declared type for `OrderState`. It is not possible anymore to pass non-sense values into `OrderService`. That is because `OrderState` enum provides no interface for creating non-sense values. So they simply cannot exists.

All logic has been kept in `OrderService`. We still need to handle cas when someone added new value to enum, which we do not count with. (the exception in default case).

## 3. Logic moved into enum

[source code](refactoring-3.phpt)

Here I have moved `OrderService::canDoTransition()` method into enum itself. 

Nice thing is that we do not need anymore external service for asking `OrderState`-related questions.

Remaining problem is that there are still lot of ifs and we still need to handle case where someone adds new value into enum which we do not count with.

## 4. Separate instance for each value

[source code](refactoring-4.phpt)

When there is behaviour same for all values of enum, it can be safely placed on enum class. Behaviour can be parametrized by providing necessary information in enum-value constructor.

## 5. Separate class implementation for each value

[source code](refactoring-5.phpt)

Now, new domain requirement:
 
> I would like to remove person who has been assigned to work on order, when order changes state to cancelled or finished.

1. I have rewritten each value as separate class (as behaviour is different for different values)
2. I have implemented doTransition() on enum parent class as it is only proper way of changing enum value
3. I have added `onActivation(Order $order)` method, which is called whenever state transition occurs.
3. I have overridden `onActivation()` on enum values with desired behaviour.
