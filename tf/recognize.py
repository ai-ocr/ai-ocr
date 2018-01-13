import os
import argparse
import sys
import json
import model
import tensorflow as tf

import input_data_r
from tensorflow.python.framework import dtypes

parser = argparse.ArgumentParser()

parser.add_argument('--cnt', type=int,
              default='500',
              help='number of images for training')
parser.add_argument('--path', type=str,
              default='',
              help='path to recognize')
parser.add_argument('--limit', type=int,
              default='100',
              help='size of one hot')

FLAGS, unparsed = parser.parse_known_args()

data = input_data_r.read_data_sets(FLAGS.cnt, FLAGS.path, dtype=dtypes.float32, reshape=True) 

# model
x = tf.placeholder("float", [None, 784])
sess = tf.Session()

with tf.variable_scope("convolutional"):
  keep_prob = tf.placeholder("float")
  y, variables = model.convolutional(x, keep_prob, FLAGS.limit)
saver = tf.train.Saver(variables)
saver.restore(sess, "convolutional.ckpt")

sys.stdout.write('[');

for i in range(0,FLAGS.cnt):
  output = sess.run(y, feed_dict={x: data.train.images[i].reshape(1, 784), keep_prob: 1.0}).flatten().tolist()
  if i > 0:
    sys.stdout.write(',')
  sys.stdout.write('{"text":"')
  if (max(output) > 0.6):
    sys.stdout.write(str(output.index(max(output))))
  sys.stdout.write('"}')

sys.stdout.write(']')

