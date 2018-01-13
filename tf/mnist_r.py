# Copyright 2016 The TensorFlow Authors. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
# ==============================================================================

"""Functions for downloading and reading MNIST data."""

from __future__ import absolute_import
from __future__ import division
from __future__ import print_function

import struct

import numpy
from six.moves import xrange  # pylint: disable=redefined-builtin

import base
from tensorflow.python.framework import dtypes

SOURCE_URL = 'http://yann.lecun.com/exdb/mnist/'


def _read32(bytestream):
  dt = numpy.dtype(numpy.uint32).newbyteorder('>')
  return numpy.frombuffer(bytestream.read(4), dtype=dt)[0]


def extract_images(fname, cnt):
  """Extract the images into a 4D uint8 numpy array [index, y, x, depth].

  Args:
    f: A file object that can be passed into a gzip reader.

  Returns:
    data: A 4D uint8 numpy array [index, y, x, depth].

  Raises:
    ValueError: If the bytestream does not start with 2051.

  """

  with open(fname, "rb") as bytestream:
    buf = bytestream.read(28 * 28 * cnt)
    data = numpy.frombuffer(buf, dtype=numpy.uint8)
    data = data.reshape(cnt, 28, 28, 1)
    return data

class DataSet(object):

  def __init__(self,
               images,
               dtype=dtypes.float32,
               reshape=True):
    """Construct a DataSet.
    one_hot arg is used only if fake_data is true.  `dtype` can be either
    `uint8` to leave the input as `[0, 255]`, or `float32` to rescale into
    `[0, 1]`.
    """
    dtype = dtypes.as_dtype(dtype).base_dtype
    if dtype not in (dtypes.uint8, dtypes.float32):
      raise TypeError('Invalid image dtype %r, expected uint8 or float32' %
                      dtype)

    self._num_examples = images.shape[0]

    # Convert shape from [num examples, rows, columns, depth]
    # to [num examples, rows*columns] (assuming depth == 1)
    if reshape:
      images = images.reshape(images.shape[0],
                              images.shape[1] * images.shape[2])
    if dtype == dtypes.float32:
      # Convert from [0, 255] -> [0.0, 1.0].
      images = images.astype(numpy.float32)
      images = numpy.multiply(images, 1.0 / 255.0)
    self._images = images

  @property
  def images(self):
    return self._images

  @property
  def num_examples(self):
    return self._num_examples

def read_data_sets(cnt,path,
                   dtype=dtypes.float32,
                   reshape=True):
  train_images = extract_images(path, cnt)

  train = DataSet(train_images, dtype=dtype, reshape=reshape)

  return base.Datasets(train=train)

